<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Spatie\Activitylog\Models\Activity;
use App\Models\User;
use Illuminate\Http\Request;
use DataTables;
use Carbon\Carbon;

class ActivityLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:activity-list');
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $logs = Activity::with(['causer', 'subject'])
                ->when($request->log_name, function($query, $logName) {
                    return $query->where('log_name', $logName);
                })
                ->when($request->event, function($query, $event) {
                    return $query->where('event', $event);
                })
                ->when($request->causer_id, function($query, $causerId) {
                    return $query->where('causer_id', $causerId);
                })
                ->when($request->subject_type, function($query, $subjectType) {
                    return $query->where('subject_type', $subjectType);
                })
                ->when($request->date_from, function($query, $dateFrom) {
                    return $query->whereDate('created_at', '>=', $dateFrom);
                })
                ->when($request->date_to, function($query, $dateTo) {
                    return $query->whereDate('created_at', '<=', $dateTo);
                })
                ->latest();

            return DataTables::eloquent($logs)
                ->addIndexColumn()
                ->addColumn('datetime', function ($log) {
                    return $log->created_at->format('M d, Y H:i:s');
                })
                ->addColumn('causer_name', function ($log) {
                    return $log->causer?->name ?? 'System';
                })
                ->addColumn('subject_name', function ($log) {
                    if (!$log->subject) {
                        return 'Deleted ' . class_basename($log->subject_type);
                    }

                    return match($log->subject_type) {
                        'App\Models\User' => $log->subject->name,
                        'App\Models\Member' => $log->subject->full_name,
                        'App\Models\Trainer' => $log->subject->full_name,
                        'App\Models\GymClass' => $log->subject->class_name,
                        'App\Models\Payment' => 'Payment #' . substr($log->subject->id, 0, 8),
                        'App\Models\Equipment' => $log->subject->equipment_name,
                        default => class_basename($log->subject_type) . ' #' . substr($log->subject_id, 0, 8)
                    };
                })
                ->addColumn('subject_type_formatted', function ($log) {
                    return class_basename($log->subject_type);
                })
                ->addColumn('event_badge', function ($log) {
                    $color = match($log->event) {
                        'created' => 'success',
                        'updated' => 'primary',
                        'deleted' => 'danger',
                        'login' => 'info',
                        'logout' => 'secondary',
                        'restored' => 'warning',
                        default => 'dark'
                    };
                    return '<span class="badge bg-' . $color . '">' . ucfirst($log->event) . '</span>';
                })
                ->addColumn('action', function ($log) {
                    $actions = '<button type="button" class="btn btn-sm btn-outline-primary" onclick="viewDetails(\'' . $log->id . '\')">';
                    $actions .= '<i class="bx bx-show me-1"></i> View';
                    $actions .= '</button>';
                    return $actions;
                })
                ->rawColumns(['event_badge', 'action'])
                ->make(true);
        }

        $users = User::select('id', 'name')->get();
        $logNames = Activity::distinct()->pluck('log_name')->filter();
        $events = Activity::distinct()->pluck('event')->filter();
        $subjectTypes = Activity::distinct()->pluck('subject_type')->map(function($type) {
            return [
                'value' => $type,
                'label' => class_basename($type)
            ];
        });

        return view('backend.activity-logs.index', compact('users', 'logNames', 'events', 'subjectTypes'));
    }

    public function show(Activity $activityLog)
    {
        $activityLog->load(['causer', 'subject']);
        
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $activityLog->id,
                'log_name' => $activityLog->log_name,
                'description' => $activityLog->description,
                'event' => $activityLog->event,
                'causer_name' => $activityLog->causer?->name ?? 'System',
                'subject_name' => $this->getSubjectName($activityLog),
                'subject_type' => class_basename($activityLog->subject_type),
                'properties' => $activityLog->properties,
                'created_at' => $activityLog->created_at->format('M d, Y H:i:s'),
                'batch_uuid' => $activityLog->batch_uuid
            ]
        ]);
    }

    public function export(Request $request)
    {
        $this->authorize('activity-export');

        $logs = Activity::with(['causer', 'subject'])
            ->when($request->log_name, function($query, $logName) {
                return $query->where('log_name', $logName);
            })
            ->when($request->event, function($query, $event) {
                return $query->where('event', $event);
            })
            ->when($request->date_from, function($query, $dateFrom) {
                return $query->whereDate('created_at', '>=', $dateFrom);
            })
            ->when($request->date_to, function($query, $dateTo) {
                return $query->whereDate('created_at', '<=', $dateTo);
            })
            ->latest()
            ->get();

        $filename = 'activity_logs_' . now()->format('Y_m_d_H_i_s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($logs) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, [
                'Date/Time',
                'Event',
                'Description',
                'User',
                'Subject Type',
                'Subject',
                'Properties'
            ]);

            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->created_at->format('Y-m-d H:i:s'),
                    $log->event,
                    $log->description,
                    $log->causer?->name ?? 'System',
                    class_basename($log->subject_type),
                    $this->getSubjectName($log),
                    json_encode($log->properties)
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function cleanup(Request $request)
    {
        $this->authorize('activity-cleanup');

        $request->validate([
            'days' => 'required|integer|min:1|max:365'
        ]);

        $cutoffDate = Carbon::now()->subDays($request->days);
        $deletedCount = Activity::where('created_at', '<', $cutoffDate)->delete();

        return response()->json([
            'success' => true,
            'message' => "Deleted {$deletedCount} activity logs older than {$request->days} days."
        ]);
    }

    private function getSubjectName($log)
    {
        if (!$log->subject) {
            return 'Deleted ' . class_basename($log->subject_type);
        }

        return match($log->subject_type) {
            'App\Models\User' => $log->subject->name,
            'App\Models\Member' => $log->subject->full_name,
            'App\Models\Trainer' => $log->subject->full_name,
            'App\Models\GymClass' => $log->subject->class_name,
            'App\Models\Payment' => 'Payment #' . substr($log->subject->id, 0, 8),
            'App\Models\Equipment' => $log->subject->equipment_name,
            default => class_basename($log->subject_type) . ' #' . substr($log->subject_id, 0, 8)
        };
    }
}

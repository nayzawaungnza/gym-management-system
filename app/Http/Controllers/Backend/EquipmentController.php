<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\EquipmentRequest;
use App\Models\Equipment;
use App\Helpers\ActivityLogHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class EquipmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view equipment', ['only' => ['index', 'show']]);
        $this->middleware('permission:create equipment', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit equipment', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete equipment', ['only' => ['destroy', 'bulkDelete']]);
    }

    /**
     * Display a listing of equipment
     */
    public function index(Request $request)
    {
        $query = Equipment::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Category filter
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Maintenance due filter
        if ($request->filled('maintenance_due')) {
            if ($request->maintenance_due === 'overdue') {
                $query->where('next_maintenance_date', '<', now());
            } elseif ($request->maintenance_due === 'due_soon') {
                $query->whereBetween('next_maintenance_date', [now(), now()->addDays(7)]);
            }
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'name');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        $equipment = $query->paginate(15)->withQueryString();

        // Statistics for dashboard
        $stats = [
            'total' => Equipment::count(),
            'operational' => Equipment::where('status', 'operational')->count(),
            'maintenance' => Equipment::where('status', 'maintenance')->count(),
            'out_of_order' => Equipment::where('status', 'out_of_order')->count(),
            'overdue_maintenance' => Equipment::where('next_maintenance_date', '<', now())->count(),
        ];

        // Categories for filter dropdown
        $categories = Equipment::distinct()->pluck('category')->filter()->sort();

        return view('backend.equipment.index', compact('equipment', 'stats', 'categories'));
    }

    /**
     * Show the form for creating new equipment
     */
    public function create()
    {
        $categories = Equipment::distinct()->pluck('category')->filter()->sort();
        return view('backend.equipment.create', compact('categories'));
    }

    /**
     * Store newly created equipment
     */
    public function store(EquipmentRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();
            
            // Handle image upload
            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image')->store('equipment', 'public');
            }

            // Calculate next maintenance date
            if ($data['maintenance_interval_days']) {
                $data['next_maintenance_date'] = Carbon::parse($data['last_maintenance_date'] ?? now())
                    ->addDays($data['maintenance_interval_days']);
            }

            // Handle specifications JSON
            if ($request->filled('specifications')) {
                $data['specifications'] = json_decode($request->specifications, true);
            }

            $equipment = Equipment::create($data);

            // Log activity
            ActivityLogHelper::log(
                'Equipment Created',
                "Equipment '{$equipment->name}' was created",
                $equipment,
                auth()->user()
            );

            DB::commit();

            return redirect()->route('backend.equipment.index')
                ->with('success', 'Equipment created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to create equipment: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified equipment
     */
    public function show(Equipment $equipment)
    {
        $equipment->load(['maintenanceLogs' => function ($query) {
            $query->latest()->limit(10);
        }]);

        // Calculate warranty status
        $warrantyStatus = 'expired';
        if ($equipment->warranty_expiry_date && $equipment->warranty_expiry_date > now()) {
            $warrantyStatus = 'active';
            $daysUntilExpiry = now()->diffInDays($equipment->warranty_expiry_date);
            if ($daysUntilExpiry <= 30) {
                $warrantyStatus = 'expiring_soon';
            }
        }

        // Calculate maintenance status
        $maintenanceStatus = 'up_to_date';
        if ($equipment->next_maintenance_date) {
            if ($equipment->next_maintenance_date < now()) {
                $maintenanceStatus = 'overdue';
            } elseif ($equipment->next_maintenance_date <= now()->addDays(7)) {
                $maintenanceStatus = 'due_soon';
            }
        }

        return view('backend.equipment.show', compact('equipment', 'warrantyStatus', 'maintenanceStatus'));
    }

    /**
     * Show the form for editing equipment
     */
    public function edit(Equipment $equipment)
    {
        $categories = Equipment::distinct()->pluck('category')->filter()->sort();
        return view('backend.equipment.edit', compact('equipment', 'categories'));
    }

    /**
     * Update the specified equipment
     */
    public function update(EquipmentRequest $request, Equipment $equipment)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();
            $originalData = $equipment->toArray();

            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete old image
                if ($equipment->image) {
                    Storage::disk('public')->delete($equipment->image);
                }
                $data['image'] = $request->file('image')->store('equipment', 'public');
            }

            // Calculate next maintenance date if maintenance interval changed
            if (isset($data['maintenance_interval_days']) && 
                $data['maintenance_interval_days'] != $equipment->maintenance_interval_days) {
                $data['next_maintenance_date'] = Carbon::parse($data['last_maintenance_date'] ?? now())
                    ->addDays($data['maintenance_interval_days']);
            }

            // Handle specifications JSON
            if ($request->filled('specifications')) {
                $data['specifications'] = json_decode($request->specifications, true);
            }

            $equipment->update($data);

            // Log activity with changes
            $changes = array_diff_assoc($data, $originalData);
            if (!empty($changes)) {
                ActivityLogHelper::log(
                    'Equipment Updated',
                    "Equipment '{$equipment->name}' was updated",
                    $equipment,
                    auth()->user(),
                    ['changes' => $changes]
                );
            }

            DB::commit();

            return redirect()->route('backend.equipment.index')
                ->with('success', 'Equipment updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to update equipment: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified equipment
     */
    public function destroy(Equipment $equipment)
    {
        try {
            DB::beginTransaction();

            $equipmentName = $equipment->name;

            // Delete associated image
            if ($equipment->image) {
                Storage::disk('public')->delete($equipment->image);
            }

            $equipment->delete();

            // Log activity
            ActivityLogHelper::log(
                'Equipment Deleted',
                "Equipment '{$equipmentName}' was deleted",
                null,
                auth()->user()
            );

            DB::commit();

            return redirect()->route('backend.equipment.index')
                ->with('success', 'Equipment deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete equipment: ' . $e->getMessage());
        }
    }

    /**
     * Bulk delete equipment
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'equipment_ids' => 'required|array',
            'equipment_ids.*' => 'exists:equipment,id'
        ]);

        try {
            DB::beginTransaction();

            $equipment = Equipment::whereIn('id', $request->equipment_ids)->get();
            $count = $equipment->count();

            foreach ($equipment as $item) {
                // Delete associated images
                if ($item->image) {
                    Storage::disk('public')->delete($item->image);
                }
                $item->delete();
            }

            // Log activity
            ActivityLogHelper::log(
                'Bulk Equipment Deletion',
                "Bulk deleted {$count} equipment items",
                null,
                auth()->user()
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Successfully deleted {$count} equipment items."
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete equipment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk update equipment status
     */
    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'equipment_ids' => 'required|array',
            'equipment_ids.*' => 'exists:equipment,id',
            'status' => 'required|in:operational,maintenance,out_of_order,retired'
        ]);

        try {
            DB::beginTransaction();

            $count = Equipment::whereIn('id', $request->equipment_ids)
                ->update(['status' => $request->status]);

            // Log activity
            ActivityLogHelper::log(
                'Bulk Equipment Status Update',
                "Updated status to '{$request->status}' for {$count} equipment items",
                null,
                auth()->user()
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Successfully updated status for {$count} equipment items."
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update equipment status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Schedule maintenance for equipment
     */
    public function scheduleMaintenance(Request $request, Equipment $equipment)
    {
        $request->validate([
            'maintenance_date' => 'required|date|after_or_equal:today',
            'maintenance_notes' => 'nullable|string|max:1000'
        ]);

        try {
            DB::beginTransaction();

            $equipment->update([
                'next_maintenance_date' => $request->maintenance_date,
                'maintenance_notes' => $request->maintenance_notes,
                'status' => 'maintenance'
            ]);

            // Log activity
            ActivityLogHelper::log(
                'Equipment Maintenance Scheduled',
                "Maintenance scheduled for '{$equipment->name}' on {$request->maintenance_date}",
                $equipment,
                auth()->user()
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Maintenance scheduled successfully.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to schedule maintenance: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export equipment data
     */
    public function export(Request $request)
    {
        $query = Equipment::query();

        // Apply same filters as index
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $equipment = $query->get();

        $filename = 'equipment_export_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($equipment) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'Name', 'Brand', 'Model', 'Serial Number', 'Category', 'Status',
                'Purchase Date', 'Purchase Price', 'Warranty Expiry', 'Location',
                'Last Maintenance', 'Next Maintenance', 'Maintenance Interval (Days)'
            ]);

            // CSV data
            foreach ($equipment as $item) {
                fputcsv($file, [
                    $item->name,
                    $item->brand,
                    $item->model,
                    $item->serial_number,
                    $item->category,
                    ucfirst($item->status),
                    $item->purchase_date?->format('Y-m-d'),
                    $item->purchase_price,
                    $item->warranty_expiry_date?->format('Y-m-d'),
                    $item->location,
                    $item->last_maintenance_date?->format('Y-m-d'),
                    $item->next_maintenance_date?->format('Y-m-d'),
                    $item->maintenance_interval_days
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
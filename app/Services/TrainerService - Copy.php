<?php

namespace App\Services;

use App\Models\Trainer;
use App\Models\GymClass;
use App\Models\ClassRegistration;
use App\Repositories\Backend\TrainerRepository;
use App\Services\Interfaces\TrainerServiceInterface;
use App\Helpers\ActivityLogHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class TrainerService implements TrainerServiceInterface
{
    protected $trainerRepository;

    public function __construct(TrainerRepository $trainerRepository)
    {
        $this->trainerRepository = $trainerRepository;
    }

    public function getTrainerElouent()
    {
        return $this->trainerRepository->getTrainerElouent();
    }

    public function getAllTrainers($filters = [])
    {
        try {
            return $this->trainerRepository->getAllTrainers($filters);
        } catch (\Exception $e) {
            Log::error('Error fetching trainers: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getTrainerById($id)
    {
        try {
            $trainer = $this->trainerRepository->getTrainerById($id);
            if (!$trainer) {
                throw new \Exception('Trainer not found');
            }
            return $trainer;
        } catch (\Exception $e) {
            Log::error('Error fetching trainer: ' . $e->getMessage());
            throw $e;
        }
    }

    public function createTrainer(array $data): Trainer
    {
        DB::beginTransaction();
        try {
            // Generate unique trainer ID
            $data['trainer_id'] = $this->generateTrainerId();
            
            // Set default values
            $data['status'] = $data['status'] ?? 'active';
            $data['hire_date'] = $data['hire_date'] ?? now();
            
            // Handle specializations as JSON
            if (isset($data['specializations']) && is_array($data['specializations'])) {
                $data['specializations'] = json_encode($data['specializations']);
            }
            
            // Handle certifications as JSON
            if (isset($data['certifications']) && is_array($data['certifications'])) {
                $data['certifications'] = json_encode($data['certifications']);
            }

            $trainer = $this->trainerRepository->createTrainer($data);

            // Log activity
            ActivityLogHelper::log(
                'trainer',
                'created',
                "Trainer {$trainer->name} created",
                $trainer->id
            );

            DB::commit();
            return $trainer;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating trainer: ' . $e->getMessage());
            throw $e;
        }
    }

    public function updateTrainer($id, array $data): Trainer
    {
        DB::beginTransaction();
        try {
            $trainer = $this->getTrainerById($id);
            
            // Handle specializations as JSON
            if (isset($data['specializations']) && is_array($data['specializations'])) {
                $data['specializations'] = json_encode($data['specializations']);
            }
            
            // Handle certifications as JSON
            if (isset($data['certifications']) && is_array($data['certifications'])) {
                $data['certifications'] = json_encode($data['certifications']);
            }

            $updatedTrainer = $this->trainerRepository->updateTrainer($id, $data);

            // Log activity
            ActivityLogHelper::log(
                'trainer',
                'updated',
                "Trainer {$updatedTrainer->name} updated",
                $updatedTrainer->id
            );

            DB::commit();
            return $updatedTrainer;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating trainer: ' . $e->getMessage());
            throw $e;
        }
    }

    public function deleteTrainer($id): bool
    {
        DB::beginTransaction();
        try {
            $trainer = $this->getTrainerById($id);
            
            // Check if trainer has active classes
            $activeClasses = GymClass::where('trainer_id', $id)
                ->where('status', 'active')
                ->count();
                
            if ($activeClasses > 0) {
                throw new \Exception('Cannot delete trainer with active classes. Please reassign or cancel classes first.');
            }

            $this->trainerRepository->deleteTrainer($id);

            // Log activity
            ActivityLogHelper::log(
                'trainer',
                'deleted',
                "Trainer {$trainer->name} deleted",
                $id
            );

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting trainer: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getTrainerSchedule($trainerId, $startDate = null, $endDate = null): array
    {
        try {
            $startDate = $startDate ? Carbon::parse($startDate) : Carbon::now()->startOfWeek();
            $endDate = $endDate ? Carbon::parse($endDate) : Carbon::now()->endOfWeek();

            return GymClass::with(['classRegistrations.member'])
                ->where('trainer_id', $trainerId)
                ->whereBetween('start_date', [$startDate, $endDate])
                ->orderBy('start_date')
                ->orderBy('start_time')
                ->get()
                ->map(function ($class) {
                    return [
                        'id' => $class->id,
                        'name' => $class->name,
                        'date' => $class->start_date,
                        'start_time' => $class->start_time,
                        'end_time' => $class->end_time,
                        'duration' => $class->duration,
                        'capacity' => $class->capacity,
                        'registered' => $class->classRegistrations->count(),
                        'status' => $class->status,
                        'location' => $class->location
                    ];
                })->toArray();
        } catch (\Exception $e) {
            Log::error('Error fetching trainer schedule: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getAvailableTrainers($date, $startTime, $endTime): \Illuminate\Database\Eloquent\Collection
    {
        try {
            $conflictingTrainerIds = GymClass::where('start_date', $date)
                ->where(function ($query) use ($startTime, $endTime) {
                    $query->whereBetween('start_time', [$startTime, $endTime])
                        ->orWhereBetween('end_time', [$startTime, $endTime])
                        ->orWhere(function ($q) use ($startTime, $endTime) {
                            $q->where('start_time', '<=', $startTime)
                              ->where('end_time', '>=', $endTime);
                        });
                })
                ->pluck('trainer_id')
                ->toArray();

            return Trainer::where('status', 'active')
                ->whereNotIn('id', $conflictingTrainerIds)
                ->get();
        } catch (\Exception $e) {
            Log::error('Error fetching available trainers: ' . $e->getMessage());
            throw $e;
        }
    }

    public function assignTrainerToClass($trainerId, $classId): bool
    {
        DB::beginTransaction();
        try {
            $trainer = $this->getTrainerById($trainerId);
            $class = GymClass::findOrFail($classId);
            
            // Check for conflicts
            $hasConflict = $this->checkTrainerAvailability(
                $trainerId,
                $class->start_date,
                $class->start_time,
                $class->end_time
            );
            
            if (!$hasConflict) {
                throw new \Exception('Trainer has a scheduling conflict for this time slot.');
            }

            $class->update(['trainer_id' => $trainerId]);

            // Log activity
            ActivityLogHelper::log(
                'trainer',
                'assigned_to_class',
                "Trainer {$trainer->name} assigned to class {$class->name}",
                $trainerId
            );

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error assigning trainer to class: ' . $e->getMessage());
            throw $e;
        }
    }

    public function removeTrainerFromClass($trainerId, $classId): bool
    {
        DB::beginTransaction();
        try {
            $trainer = $this->getTrainerById($trainerId);
            $class = GymClass::findOrFail($classId);
            
            $class->update(['trainer_id' => null]);

            // Log activity
            ActivityLogHelper::log(
                'trainer',
                'removed_from_class',
                "Trainer {$trainer->name} removed from class {$class->name}",
                $trainerId
            );

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error removing trainer from class: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getTrainerStats($trainerId, $period = 'month'): array
    {
        try {
            $trainer = $this->getTrainerById($trainerId);
            $startDate = $this->getStartDateForPeriod($period);
            $endDate = Carbon::now();

            $classes = GymClass::where('trainer_id', $trainerId)
                ->whereBetween('start_date', [$startDate, $endDate])
                ->get();

            $totalClasses = $classes->count();
            $completedClasses = $classes->where('status', 'completed')->count();
            $cancelledClasses = $classes->where('status', 'cancelled')->count();
            
            $totalParticipants = ClassRegistration::whereIn('class_id', $classes->pluck('id'))
                ->where('status', 'confirmed')
                ->count();

            $averageParticipants = $totalClasses > 0 ? round($totalParticipants / $totalClasses, 1) : 0;
            $completionRate = $totalClasses > 0 ? round(($completedClasses / $totalClasses) * 100, 1) : 0;

            return [
                'total_classes' => $totalClasses,
                'completed_classes' => $completedClasses,
                'cancelled_classes' => $cancelledClasses,
                'total_participants' => $totalParticipants,
                'average_participants' => $averageParticipants,
                'completion_rate' => $completionRate,
                'period' => $period
            ];
        } catch (\Exception $e) {
            Log::error('Error fetching trainer stats: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getTrainerEarnings($trainerId, $month = null, $year = null): array
    {
        try {
            $trainer = $this->getTrainerById($trainerId);
            $month = $month ?? Carbon::now()->month;
            $year = $year ?? Carbon::now()->year;

            $classes = GymClass::where('trainer_id', $trainerId)
                ->whereMonth('start_date', $month)
                ->whereYear('start_date', $year)
                ->where('status', 'completed')
                ->get();

            $baseEarnings = $classes->count() * ($trainer->hourly_rate ?? 50);
            
            $participantBonus = 0;
            foreach ($classes as $class) {
                $participants = ClassRegistration::where('class_id', $class->id)
                    ->where('status', 'confirmed')
                    ->count();
                $participantBonus += $participants * 2; // $2 per participant bonus
            }

            $totalEarnings = $baseEarnings + $participantBonus;

            return [
                'base_earnings' => $baseEarnings,
                'participant_bonus' => $participantBonus,
                'total_earnings' => $totalEarnings,
                'classes_taught' => $classes->count(),
                'month' => $month,
                'year' => $year
            ];
        } catch (\Exception $e) {
            Log::error('Error calculating trainer earnings: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getDashboardData($trainerId = null): array
    {
        try {
            $today = Carbon::today();
            $thisWeek = Carbon::now()->startOfWeek();
            $thisMonth = Carbon::now()->startOfMonth();

            if ($trainerId) {
                // Individual trainer dashboard
                $trainer = $this->getTrainerById($trainerId);
                
                $todayClasses = GymClass::where('trainer_id', $trainerId)
                    ->whereDate('start_date', $today)
                    ->with(['classRegistrations.member'])
                    ->orderBy('start_time')
                    ->get();

                $weeklyStats = $this->getTrainerStats($trainerId, 'week');
                $monthlyStats = $this->getTrainerStats($trainerId, 'month');
                $earnings = $this->getTrainerEarnings($trainerId);

                return [
                    'trainer' => $trainer,
                    'today_classes' => $todayClasses,
                    'weekly_stats' => $weeklyStats,
                    'monthly_stats' => $monthlyStats,
                    'earnings' => $earnings
                ];
            } else {
                // Admin dashboard for all trainers
                $totalTrainers = Trainer::count();
                $activeTrainers = Trainer::where('status', 'active')->count();
                
                $todayClasses = GymClass::whereDate('start_date', $today)
                    ->with(['trainer', 'classRegistrations'])
                    ->get();

                $topTrainers = $this->getTopPerformingTrainers(5);

                return [
                    'total_trainers' => $totalTrainers,
                    'active_trainers' => $activeTrainers,
                    'today_classes' => $todayClasses,
                    'top_trainers' => $topTrainers
                ];
            }
        } catch (\Exception $e) {
            Log::error('Error fetching dashboard data: ' . $e->getMessage());
            throw $e;
        }
    }

    public function checkTrainerAvailability($trainerId, $date, $startTime, $endTime): bool
    {
        try {
            $conflictingClasses = GymClass::where('trainer_id', $trainerId)
                ->where('start_date', $date)
                ->where('status', '!=', 'cancelled')
                ->where(function ($query) use ($startTime, $endTime) {
                    $query->whereBetween('start_time', [$startTime, $endTime])
                        ->orWhereBetween('end_time', [$startTime, $endTime])
                        ->orWhere(function ($q) use ($startTime, $endTime) {
                            $q->where('start_time', '<=', $startTime)
                              ->where('end_time', '>=', $endTime);
                        });
                })
                ->count();

            return $conflictingClasses === 0;
        } catch (\Exception $e) {
            Log::error('Error checking trainer availability: ' . $e->getMessage());
            return false;
        }
    }

    public function getTopPerformingTrainers($limit = 5): \Illuminate\Database\Eloquent\Collection
    {
        try {
            $startDate = Carbon::now()->startOfMonth();
            $endDate = Carbon::now();

            return Trainer::withCount([
                'gymClasses as classes_this_month' => function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('start_date', [$startDate, $endDate])
                          ->where('status', 'completed');
                }
            ])
            ->with(['gymClasses' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                      ->where('status', 'completed')
                      ->withCount(['classRegistrations as participants' => function ($q) {
                          $q->where('status', 'confirmed');
                      }]);
            }])
            ->where('status', 'active')
            ->orderBy('classes_this_month', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($trainer) {
                $totalParticipants = $trainer->gymClasses->sum('participants');
                return [
                    'id' => $trainer->id,
                    'name' => $trainer->name,
                    'email' => $trainer->email,
                    'specializations' => $trainer->specializations,
                    'classes_this_month' => $trainer->classes_this_month,
                    'total_participants' => $totalParticipants,
                    'average_participants' => $trainer->classes_this_month > 0 
                        ? round($totalParticipants / $trainer->classes_this_month, 1) 
                        : 0
                ];
            });
        } catch (\Exception $e) {
            Log::error('Error fetching top performing trainers: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getTrainersBySpecialization($specialization): \Illuminate\Database\Eloquent\Collection
    {
        try {
            return Trainer::where('status', 'active')
                ->where('specializations', 'like', "%{$specialization}%")
                ->get();
        } catch (\Exception $e) {
            Log::error('Error fetching trainers by specialization: ' . $e->getMessage());
            throw $e;
        }
    }

    public function updateTrainerStatus($id, $status): Trainer
    {
        DB::beginTransaction();
        try {
            $trainer = $this->getTrainerById($id);
            $oldStatus = $trainer->status;
            
            $trainer->update(['status' => $status]);

            // Log activity
            ActivityLogHelper::log(
                'trainer',
                'status_updated',
                "Trainer {$trainer->name} status changed from {$oldStatus} to {$status}",
                $id
            );

            DB::commit();
            return $trainer;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating trainer status: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getTrainerClasses($trainerId, $status = 'active'): \Illuminate\Database\Eloquent\Collection
    {
        try {
            return GymClass::where('trainer_id', $trainerId)
                ->where('status', $status)
                ->with(['classRegistrations.member'])
                ->orderBy('start_date')
                ->orderBy('start_time')
                ->get();
        } catch (\Exception $e) {
            Log::error('Error fetching trainer classes: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getTrainerPerformance(Trainer $trainer): array
    {
        try {
            $stats = $this->getTrainerStats($trainer->id, 'month');
            return [
                'trainer_id' => $trainer->id,
                'name' => $trainer->name,
                'performance_metrics' => $stats
            ];
        } catch (\Exception $e) {
            Log::error('Error fetching trainer performance: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getTrainerClassHistory($trainerId, $startDate = null, $endDate = null): \Illuminate\Database\Eloquent\Collection
    {
        try {
            $startDate = $startDate ? Carbon::parse($startDate) : Carbon::now()->subMonth();
            $endDate = $endDate ? Carbon::parse($endDate) : Carbon::now();

            return GymClass::where('trainer_id', $trainerId)
                ->whereBetween('start_date', [$startDate, $endDate])
                ->with(['classRegistrations.member'])
                ->orderBy('start_date', 'desc')
                ->get();
        } catch (\Exception $e) {
            Log::error('Error fetching trainer class history: ' . $e->getMessage());
            throw $e;
        }
    }

    private function generateTrainerId()
    {
        $lastTrainer = Trainer::orderBy('id', 'desc')->first();
        $nextId = $lastTrainer ? $lastTrainer->id + 1 : 1;
        return 'TRN' . str_pad($nextId, 6, '0', STR_PAD_LEFT);
    }

    private function getStartDateForPeriod($period)
    {
        switch ($period) {
            case 'week':
                return Carbon::now()->startOfWeek();
            case 'month':
                return Carbon::now()->startOfMonth();
            case 'year':
                return Carbon::now()->startOfYear();
            default:
                return Carbon::now()->startOfMonth();
        }
    }

    public function getActiveTrainers(): \Illuminate\Database\Eloquent\Collection
    {
        return Trainer::where('status', 'active')->get();
    }

    public function getPaginatedTrainers(Request $request): LengthAwarePaginator
    {
        return $this->trainerRepository->getPaginatedTrainers($request);
    }
}
<?php
namespace App\Repositories\Backend;
use App\Models\GymClass;
use App\Repositories\BaseRepository;

class ClassRepository extends BaseRepository
{
    public function model()
    {
        return GymClass::class;
    }


    public function getClassesEloquent()
    {
        return GymClass::query()
            ->select('gym_classes.*')
            ->with(['trainer', 'members'])
            ->orderBy('schedule_day', 'asc');
    }

    
    public function getClass($gymClass)
    {
        return $this->getById($gymClass->id);
    }

    public function create(array $data)
    {
        $gymClass = GymClass::create([
            'trainer_id' => $data['trainer_id'],
            'class_name' => $data['class_name'],
            'description' => $data['description'],
            'class_type' => $data['class_type'],
            'schedule_day' => $data['schedule_day'],
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'duration_minutes' => $data['duration_minutes'],
            'max_capacity' => $data['max_capacity'],
            'current_capacity' => 0, // Initialize current capacity to 0
            'price' => $data['price'],
            'room' => $data['room'] ?? null,
            'equipment_needed' => $data['equipment_needed'] ?? null,
            'difficulty_level' => $data['difficulty_level'] ?? null,
            'is_active' => true, // Default to active
        ]);
        // Optionally, you can attach members if needed
        if (isset($data['members']) && is_array($data['members'])) {
            $gymClass->members()->attach($data['members']);
        }
        // Optionally, you can attach the trainer if needed
        if (isset($data['trainer_id'])) {
            $gymClass->trainer()->associate($data['trainer_id']);
        }
      
        // Optionally, you can log the activity
        $activity_data['subject'] = $gymClass;
        $activity_data['event'] = config('constants.ACTIVITY_LOG.CREATED_EVENT_NAME');
        $activity_data['description'] = sprintf('Gym Class (%s) was created by %s.', $gymClass->class_name, auth()->user()->name);
        $this->logActivity($activity_data);

        // Save the class
        $gymClass->save();
        return $gymClass;
    }

    public function update(GymClass $class, array $data)
    {
        $class->update([
            'trainer_id' => $data['trainer_id'] ?? $class->trainer_id,
            'class_name' => $data['class_name'] ?? $class->class_name,
            'description' => $data['description'] ?? $class->description,
            'class_type' => $data['class_type'] ?? $class->class_type,
            'schedule_day' => $data['schedule_day'] ?? $class->schedule_day,
            'start_time' => $data['start_time'] ?? $class->start_time,
            'end_time' => $data['end_time'] ?? $class->end_time,
            'duration_minutes' => $data['duration_minutes'] ?? $class->duration_minutes,
            'max_capacity' => $data['max_capacity'] ?? $class->max_capacity,
            'current_capacity' => $data['current_capacity'] ?? $class->current_capacity,
            'price' => $data['price'] ?? $class->price,
            'room' => $data['room'] ?? $class->room,
            'equipment_needed' => $data['equipment_needed'] ?? $class->equipment_needed,
            'difficulty_level' => $data['difficulty_level'] ?? $class->difficulty_level,
            'is_active' => $data['is_active'] ?? $class->is_active,
        ]);
        // Optionally, you can log the activity
        $activity_data['subject'] = $class;
        $activity_data['event'] = config('constants.ACTIVITY_LOG.UPDATED_EVENT_NAME');
        $activity_data['description'] = sprintf('Gym Class (%s) was updated by %s.', $class->class_name, auth()->user()->name);
        $this->logActivity($activity_data);
        // Save the class
        $class->save();
        return $class;
    }

    public function destroy(GymClass $class)
    {
        // Optionally, you can log the activity
        $activity_data['subject'] = $class;
        $activity_data['event'] = config('constants.ACTIVITY_LOG.DELETED_EVENT_NAME');
        $activity_data['description'] = sprintf('Gym Class (%s) was deleted by %s.', $class->class_name, auth()->user()->name);
        $this->logActivity($activity_data);

        // Delete the class
        return $class->delete();
        
    }

    public function cancel(GymClass $class)
    {
        return $class->update(['status' => 'canceled']);
    }
    public function checkAvailability(GymClass $class)
    {
        // Assuming a method to check if the class is full
        return !$class->isFull();
    }
    public function registerMember(int $memberId, int $classId)
    {
        $class = $this->getById($classId);
        if ($class && $this->checkAvailability($class)) {
            return $class->members()->attach($memberId);
        }
        return false;
    }
    public function getUpcomingClasses()
    {
        return GymClass::where('schedule_day', '>=', now())
            ->orderBy('schedule_day', 'asc')
            ->get();
    }
    public function getCompletedClasses()
    {
        return GymClass::where('schedule_day', '<', now())
            ->orderBy('schedule_day', 'desc')
            ->get();
    }
    public function getClassesByTrainer($trainerId)
    {
        return GymClass::where('trainer_id', $trainerId)
            ->orderBy('schedule_day', 'asc')
            ->get();
    }
    public function getClassesByMember($memberId)
    {
        return GymClass::whereHas('members', function($query) use ($memberId) {
            $query->where('member_id', $memberId);
        })->orderBy('schedule_day', 'asc')->get();
    }
}
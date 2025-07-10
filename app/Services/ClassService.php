<?php

namespace App\Services;

use App\Models\GymClass;
use App\Repositories\Backend\ClassRepository;
use App\Services\Interfaces\ClassServiceInterface;
use Illuminate\Http\Request;

class ClassService implements ClassServiceInterface
{
    protected $classRepository;

    public function __construct(ClassRepository $classRepository)
    {
        $this->classRepository = $classRepository;
    }

    

    public function getClass(GymClass $class)
    {
        return $this->classRepository->getClass($class);
    }
    

    public function getClassesEloquent()
    {
        return $this->classRepository->getClassesEloquent();
    }

    public function createClass(array $data)
    {
        return $this->classRepository->create($data);
    }

    public function updateClass(GymClass $class, array $data)
    {
        return $this->classRepository->update($class, $data);
    }

    public function deleteClass(GymClass $class)
    {
        return $this->classRepository->destroy($class);
    }

    public function cancelClass(GymClass $class)
    {
        return $this->classRepository->cancel($class);
    }

    public function checkAvailability(GymClass $class)
    {
        return !$class->isFull();
    }

    public function registerMemberToClass($memberId, $classId)
    {
        return $this->classRepository->registerMember($memberId, $classId);
    }
}
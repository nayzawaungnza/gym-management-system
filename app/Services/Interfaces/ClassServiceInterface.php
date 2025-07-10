<?php

namespace App\Services\Interfaces;

use App\Models\GymClass;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface ClassServiceInterface
{

    public function getClassesEloquent();
    public function getClass(GymClass $class);

    public function createClass(array $data);

    public function updateClass(GymClass $class, array $data);

    public function deleteClass(GymClass $class);

    public function cancelClass(GymClass $class);

    public function checkAvailability(GymClass $class);

    public function registerMemberToClass( $memberId,  $classId);
}
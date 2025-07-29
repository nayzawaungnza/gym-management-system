<?php

namespace App\Services\Interfaces;

use App\Enums\IsAdminStatusEnum;
use App\Models\User;

interface UserServiceInterface
{
   public function getUsers($request, $is_active = null); 
   public function getUser($id);
   public function changeStatus(User $user);
   public function create(array $request);
   public function subscribe(array $request);
   public function update(User $user, array $request);
   public function destroy(User $user);
   public function getUsersCount($filter);

   public function checkActiveUser(array $data);
    public function getUserEloquent();

    public function getUserRolesPluckName(User $user);

    public function getClientEloquent();

    public function getSubscriberEloquent();

    public function getClients();
    public function getSubscribers();
}
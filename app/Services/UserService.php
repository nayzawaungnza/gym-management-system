<?php

namespace App\Services;
use Exception;
use App\Models\User;
use InvalidArgumentException;
use App\Enums\IsAdminStatusEnum;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Repositories\Backend\UserRepository;
use App\Services\Interfaces\UserServiceInterface;

class UserService implements UserServiceInterface
{
    protected $userRepository;
    /**
     * Create a new class instance.
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getUsers($request, $is_active = null){
        
    }

    public function getUser($id){

    }
    public function create(array $data){

       
        DB::beginTransaction();
        try {
            $user = $this->userRepository->create($data);
        } catch (Exception $exc) {
            DB::rollBack();
            Log::error('User Registeration Error:'.$exc->getMessage());
            throw new InvalidArgumentException('Unable to register'); 
        }
        DB::commit();
        return $user;
    }
    
    public function subscribe(array $data){

       
        DB::beginTransaction();
        try {
            $user = $this->userRepository->subscribe($data);
        } catch (Exception $exc) {
            DB::rollBack();
            Log::error('User subscribe Error:'.$exc->getMessage());
            throw new InvalidArgumentException('Unable to subscribe'); 
        }
        DB::commit();
        return $user;
    }

    public function update(User $user, array $data){
        
        DB::beginTransaction();
        try {
            $result = $this->userRepository->update($user, $data);
        } catch (Exception $exc) {
            DB::rollBack();
            Log::error($exc->getMessage());
            throw new InvalidArgumentException('Unable to update user account');
        }
        DB::commit();

        return $result;
    }

    public function destroy(User $user){
        DB::beginTransaction();
        try {
            $result = $this->userRepository->destroy($user);
        } catch (Exception $exc) {
            DB::rollBack();
            Log::error($exc->getMessage());
            throw new InvalidArgumentException('Unable to delete user account');
        }
        DB::commit();

        return $result;
    }

    public function changeStatus(User $user){
        DB::beginTransaction();
        try {
            $result = $this->userRepository->changeStatus($user);
        } catch (Exception $exc) {
            DB::rollBack();
            Log::error($exc->getMessage());
            throw new InvalidArgumentException('Unable to active user accounts');
        }
        DB::commit();

        return $result;
    }

    public function getUsersCount($filter){
        return $this->userRepository->getUsersCount($filter);
    }

    public function checkActiveUser(array $data){
        $result = $this->userRepository->where('email', $data['email'])->get();
        return $result;
    }

    public function getUserEloquent(){
        return $this->userRepository->getUserEloquent();
    }

    public function getUserRolesPluckName($user)
    {
        return $user->roles->pluck('name', 'name')->all();
    }
    
    public function getClientEloquent() {
        return $this->userRepository->getClientEloquent();
    }

    public function getSubscriberEloquent()
    {
        return $this->userRepository->getSubscriberEloquent();
    }

    public function getClients()
    {
        return $this->userRepository->getClients();
    }

    public function getSubscribers()
    {
        return $this->userRepository->getSubscribers();
    }
}
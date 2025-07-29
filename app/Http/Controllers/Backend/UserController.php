<?php

namespace App\Http\Controllers\Backend;

use DataTables;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\RoleService;
use App\Services\UserService;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\CreateUserRequest;
use App\Http\Requests\User\UpdateUserRequest;

class UserController extends Controller
{
    protected $userService;
    protected $roleService;

    public function __construct(UserService $userService, RoleService $roleService)
    {
        $this->middleware('permission:member-list', ['only' => ['index']]);
        $this->middleware('permission:member-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:member-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:member-delete', ['only' => ['destroy']]);
        $this->userService = $userService;
        $this->roleService = $roleService;
    }

    public function index(Request $request)
    {

        $users = $this->userService->getUserEloquent()->paginate(10);
        return view('backend.users.index', compact('users'));

    }

    public function create()
    {
        $roles = $this->roleService->getRolesPluckName();
        //dd($roles);
        $url = route('users.store');
        return view('backend.users.create', compact('url', 'roles'));
    }

    public function store(CreateUserRequest $request)
    {
        $this->userService->create($request->all());
        return redirect('admin/users')->with('status', 'User has been added successfully.');
    }

    public function edit(User $user)
    {
       
        //dd($user->toArray());
        $roles = $this->roleService->getRolesPluckName();
        $userRole = $this->userService->getUserRolesPluckName($user);
        //dd($userRole);
        $url = route('users.update', $user->id);
        return view('backend.users.edit', compact('user', 'roles', 'userRole', 'url'));
    }


    public function update(UpdateUserRequest $request, User $user)
    {
        //dd($request->all());
        $this->userService->update($user, $request->all());
        return redirect('admin/users')->with('status', 'Admin Account has been updated successfully.');
    }

    public function changeStatus(User $user)
    {
            $result = $this->userService->changeStatus($user);
            return redirect('admin/users')->with('status', 'Admin Account status has been updated successfully.');
        
    }
    public function destroy(User $user)
    {
        $this->userService->destroy($user);
        return redirect()->route('users.index')->with('status', 'Admin Account has been deleted successfully.');
    }
    public function show(User $user)
    {
        // This method can be used to show user details if needed
        return view('backend.users.show', compact('user'));
    }
    
}
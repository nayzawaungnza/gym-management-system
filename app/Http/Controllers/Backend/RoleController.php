<?php

namespace App\Http\Controllers\Backend;


use DataTables;
use Illuminate\Http\Request;
use App\Services\RoleService;
use Illuminate\Http\JsonResponse;
use Spatie\Permission\Models\Role;
use App\Services\PermissionService;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\Role\CreateRoleRequest;
use App\Http\Requests\Role\UpdateRoleRequest;

class RoleController extends Controller
{
    /**
     * @var RoleService
     */
    protected $roleService;
    /**
     * @var PermissionService
     */
    protected $permissionService;

    /**
     * RoleController constructor.
     *
     * @param RoleService $roleService
     * @param PermissionService $permissionService
     */
    public function __construct(RoleService $roleService, PermissionService $permissionService)
    {
        $this->roleService = $roleService;
        $this->permissionService = $permissionService;
        
        $this->middleware('permission:role-list|role-create|role-edit|role-delete', ['only' => ['index', 'store']]);
        $this->middleware('permission:role-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:role-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:role-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        
        
        if ($request->ajax()) {
            $eloquent = $this->roleService->getRoleEloquent();
            return DataTables::eloquent($eloquent)
                ->addIndexColumn()
                ->addColumn('users_count', function ($row) {
                    return $row->users->count();
                })
                ->addColumn('permissions_count', function ($row) {
                    return $row->permissions->count();
                })
                ->addColumn('created_at', function ($row) {
                    return $row->created_at->format('Y-m-d H:i:s');
                })
                ->addColumn('action', function ($row) {
                    $btn = '<div class=" m-sm-n1">';
                    $btn = $btn . '<div class="my-1 text-center"><a rel="tooltip" class="button-size btn btn-sm btn-success" href="' . route('roles.edit', $row->id) . '"
                                data-original-title="" title="Edit">
                                <i class="fas fa-edit"></i>
                                <div class="ripple-container"></div>
                                </a></div>';
                    $btn = $btn . '<div class="my-1 text-center"><a rel="tooltip" class="button-size btn btn-sm btn-primary" href="' . route('roles.show', $row->id) . '"
                                data-original-title="" title="Show">
                                <i class="fas fa-eye"></i>
                                <div class="ripple-container"></div>
                            </a></div>';
                    $btn = $btn . '<div class="my-1 text-center"><form action="' . route('roles.destroy', $row->id) . '" method="POST" id="del-role-' . $row->id . '" class="d-inline">
                                <input type="hidden" name="_token" value="' . csrf_token() . '">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="button" class="button-size btn btn-sm btn-danger destroy_btn" data-original-title="" data-origin="del-role-' . $row->id . '" title="Delete">
                                <i class="fas fa-trash"></i>
                                </button>                                                    
                                </form></div>';
                    $btn = $btn . '</div>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('backend.roles.index');
        // ->with('i', ($request->input('page', 1) - 1) * 5);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $permission = $this->permissionService->getPermissions();
        //dd($permissions->toArray());
        $url = route('roles.store');
        return view('backend.roles.create', compact('permission', 'url'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateRoleRequest $request)
    {
        //dd($request->all());
        //dd($guard);
        $this->roleService->create($request->all());
        return redirect()->route('roles.index')->with('status', 'Role created successfully.');
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Role $role)
    {
        $rolePermissions = $this->permissionService->getRolePermission($role->id);
        return view('backend.roles.show', compact('role', 'rolePermissions'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Role $role)
    {
        //$permission = $this->permissionService->getPermissions();
        $permission = $this->permissionService->getPermissionsByGuard($role->guard_name);
        //dd($permission);
        $rolePermissions = $this->permissionService->getRolePermissions($role->id);
        $url = route('roles.update', $role->id);
        return view('backend.roles.edit', compact('role', 'url', 'permission', 'rolePermissions'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRoleRequest $request, Role $role)
    {
        $this->roleService->update($role, $request->all());
        return redirect()->route('roles.index')
            ->with('status', 'Role updated successfully.');
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {
        $this->roleService->destroy($role);
        return redirect()->route('roles.index')
            ->with('status', 'Role deleted successfully.');
    }

    public function getPermissionsByGuard(string $guardName): JsonResponse
    {
        $permissions = $this->permissionService->getPermissionsByGuard($guardName);
        return response()->json($permissions);
        //return response()->json(['permissions' => $permissions]);
    }
}
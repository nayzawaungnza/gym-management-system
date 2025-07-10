<?php

namespace App\Repositories\Backend;

use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;

class RoleRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return Role::class;
    }

    /**
     * @param array $data
     *
     * @return Role
     */
    public function create(array $data) : Role
    {
        // Validate permissions exist for the 'admin' guard
        $validPermissions = Permission::whereIn('id', $data['permission'])
                            ->where('guard_name', 'web')
                            ->pluck('id')
                            ->toArray();

        Log::info('Valid Permissions:', $validPermissions);

        if (count($validPermissions) !== count($data['permission'])) {
        throw new \Exception('One or more permissions are invalid.');
        }

        // Create role
        $role = Role::create([
        'name'   => $data['name'],
        //'guard_name' => $data['guard_name'],
        'guard_name' => 'web',
        ]);

        Log::info('Created Role ID:', ['role_id' => $role->id]);

        // Wrap the syncPermissions in a DB transaction
        DB::transaction(function() use ($role, $validPermissions) {
        $role->syncPermissions($validPermissions);
        Log::info('Permissions synced successfully for role:', ['role_id' => $role->id]);
        });

        // Check if the role has been assigned permissions
        $rolePermissions = DB::table('model_has_permissions')
            ->where('model_id', $role->id)
            ->where('model_type', Role::class)
            ->pluck('permission_id')
            ->toArray();

        Log::info('Role Permissions after sync:', $rolePermissions);

        // save activity in activitylog
        $activity_data['subject'] = $role;
        $activity_data['event'] = config('constants.ACTIVITY_LOG.CREATED_EVENT_NAME');
        $activity_data['description'] = sprintf('Admin(%s) created Role(%s).', auth()->user()->name, $role->name);
        saveActivityLog($activity_data);
        return $role;
    }

    /**
     * @param Agent  $agent
     * @param array $data
     *
     * @return mixed
     */
    public function update(Role $role, array $data) : Role
    {
        // Update role details if they are provided
        $role->name = isset($data['name']) ? $data['name'] : $role->name;
        $role->guard_name = isset($data['guard_name']) ? $data['guard_name'] : $role->guard_name;

        // Check if role details have changed before saving
        if ($role->isDirty()) {
            $role->save();
            Log::info('Role updated:', ['role_id' => $role->id, 'role_name' => $role->name]);
        }

        // Validate permissions exist for the 'admin' guard
        $validPermissions = Permission::whereIn('id', $data['permission'])
                            ->where('guard_name', 'web')
                            ->pluck('id')
                            ->toArray();

        Log::info('Valid permissions to sync:', $validPermissions);

        if (count($validPermissions) !== count($data['permission'])) {
            throw new \Exception('One or more permissions are invalid.');
        }

        // Sync permissions regardless of role changes
        DB::transaction(function() use ($role, $validPermissions) {
            $role->syncPermissions($validPermissions);
            Log::info('Permissions synced for role:', ['role_id' => $role->id]);
        });

        // Save activity in activity log after refreshing the role
        $activity_data['subject'] = $role->refresh();
        $activity_data['event'] = config('constants.ACTIVITY_LOG.UPDATED_EVENT_NAME');
        $activity_data['description'] = sprintf('Admin(%s) updated Role(%s).', auth()->user()->name, $role->name);
        saveActivityLog($activity_data);

        return $role;
    }


    /**
     * @param Role $role
     */
    public function destroy(Role $role)
    {
        $deleted = $this->deleteById($role->id);

        if ($deleted) {
            $role->save();
        }

        // save activity in activitylog
        $activity_data['subject'] = $role;
        $activity_data['event'] = config('constants.ACTIVITY_LOG.DELETED_EVENT_NAME');
        $activity_data['description'] = sprintf('Admin(%s) delete Role(%s).', auth()->user()->name, $role->name);
        saveActivityLog($activity_data);
    }

    /**
     * return Eloquent
     */
    public function getRoleEloquent()
    {
        return Role::query();
    }
}
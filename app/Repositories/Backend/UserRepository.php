<?php

namespace App\Repositories\Backend;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\Trainer; // Import Trainer model
use App\Models\Member;   // Import Member model
use App\Models\MembershipType; // Import MembershipType model
use Exception;
// Removed: use App\Repositories\ImageRepository;

class UserRepository extends BaseRepository
{
    public function model()
    {
        return User::class;
    }

    public function getUsers($request, $is_active = null)
    {
        $query = User::query();

        if ($is_active !== null) {
            $query->where('is_active', $is_active);
        }

        // Example: search by name or email
        if ($request->has('search') && !empty($request->search)) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        // Example: order by
        $query->orderBy($request->get('sort_by', 'created_at'), $request->get('order_by', 'desc'));

        // Return paginated results for general use, or adjust for DataTables if needed
        return $query->paginate($request->get('per_page', 10));
    }

    public function getUser($id)
    {
        return User::with('roles')->findOrFail($id);
    }

    public function create(array $data)
    {
        DB::beginTransaction();
        try {
            $profilePhotoPath = null;
            $path_name = 'users';

            if (isset($data['profile_photo']) && $data['profile_photo']) {
                $profilePhotoPath = $this->uploadFile($data['profile_photo'], $path_name);
            }

            // Determine is_admin based on the provided role
            $isAdminValue = $this->getIsAdminValueForRole($data['role'] ?? 'member');

            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => isset($data['password']) ? Hash::make($data['password']) : Hash::make('password'),
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'] ?? null,
                'date_of_birth' => $data['date_of_birth'] ?? null,
                'gender' => $data['gender'] ?? null,
                'emergency_contact' => $data['emergency_contact'] ?? null,
                'emergency_phone' => $data['emergency_phone'] ?? null,
                'profile_photo' => $profilePhotoPath,
                'is_active' => isset($data['is_active']) ? $data['is_active'] : true,
                'is_admin' => $isAdminValue,
                'remember_token' => $data['remember_token'] ?? null,
            ]);

            // Assign the single role
            if (isset($data['role'])) {
                $user->assignRole($data['role']);

                // After assigning role, create associated record if applicable
                $nameParts = $this->splitFullName($data['name']);

                if (Str::lower($data['role']) === 'trainer') {
                    Trainer::create([
                        'user_id' => $user->id,
                        'trainer_id' => 'TRN-' . uniqid(), // Generate a unique trainer ID
                        'first_name' => $nameParts['first_name'],
                        'last_name' => $nameParts['last_name'] ?? 'TRN',
                        'email' => $user->email,
                        'phone' => $user->phone,
                        'specialization' => $data['specialization'] ?? 'General Fitness', // Add to form if needed
                        'certifications' => $data['certifications'] ?? [], // Add to form if needed
                        'hire_date' => $data['hire_date'] ?? now(),
                        'hourly_rate' => $data['hourly_rate'] ?? 0.00,
                        'bio' => $data['bio'] ?? null,
                        'profile_photo' => $profilePhotoPath,
                        'is_active' => $user->is_active,
                    ]);
                } elseif (Str::lower($data['role']) === 'member') {
                    // Attempt to get a default membership type, or handle if none exists
                    $defaultMembershipType = MembershipType::active()->first();
                    if (!$defaultMembershipType) {
                        // Log or throw an error if no default membership type is found
                        Log::warning('No active MembershipType found for new member. Member will be created without membership_type_id.');
                    }

                    Member::create([
                        'user_id' => $user->id,
                        'membership_type_id' => $defaultMembershipType->id ?? null,
                        'member_id' => 'MEM-' . uniqid(), // Generate a unique member ID
                        'first_name' => $nameParts['first_name'],
                        'last_name' => $nameParts['last_name'] ?? 'MEM',
                        'email' => $user->email,
                        'phone' => $user->phone,
                        'date_of_birth' => $user->date_of_birth,
                        'gender' => $user->gender,
                        'address' => $user->address,
                        'emergency_contact_name' => $user->emergency_contact,
                        'emergency_contact_phone' => $user->emergency_phone,
                        'join_date' => now(),
                        'membership_start_date' => now(),
                        'membership_end_date' => now()->addMonths(1), // Example: 1 month default
                        'status' => 'active',
                        'profile_photo' => $profilePhotoPath,
                        'medical_conditions' => $data['medical_conditions'] ?? [], // Add to form if needed
                        'fitness_goals' => $data['fitness_goals'] ?? [], // Add to form if needed
                        'preferred_workout_time' => $data['preferred_workout_time'] ?? null,
                        'referral_source' => $data['referral_source'] ?? null,
                    ]);
                }
            }

            // save activity in activitylog
            $activity_data['subject'] = $user;
            $activity_data['event'] = config('constants.ACTIVITY_LOG.CREATED_EVENT_NAME');
            $model_type = (class_basename(auth()->user()->getModel()) === config('constants.LABEL_NAME.USER'))
                ? 'User'
                : class_basename(auth()->user()->getModel());
            $activity_data['description'] = sprintf('%s(%s) created User (%s).', $model_type, auth()->user()->name, $user->name);
            saveActivityLog($activity_data);

            DB::commit();
            return $user;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to create user: ' . $e->getMessage(), ['data' => $data, 'exception' => $e]);
            throw $e;
        }
    }

    

    public function update(User $user, array $data)
    {
        DB::beginTransaction();
        try {
            $path_name = 'users';

            $user->name = $data['name'] ?? $user->name;
            $user->email = $data['email'] ?? $user->email;
            $user->phone = $data['phone'] ?? null;
            $user->address = $data['address'] ?? $user->address;
            $user->date_of_birth = $data['date_of_birth'] ?? $user->date_of_birth;
            $user->gender = $data['gender'] ?? $user->gender;
            $user->emergency_contact = $data['emergency_contact'] ?? $user->emergency_contact;
            $user->emergency_phone = $data['emergency_phone'] ?? $user->emergency_phone;
            $user->is_active = $data['is_active'] ?? $user->is_active;

            // Handle password update
            if (isset($data['password']) && !empty($data['password'])) {
                $user->password = Hash::make($data['password']);
            }

            // Handle profile photo upload/update/removal
            if (isset($data['profile_photo']) && $data['profile_photo']) {
                // Delete old profile photo file from storage if it exists
                if ($user->profile_photo) {
                    $this->deleteFile($user->profile_photo);
                }
                // Upload new profile photo
                $user->profile_photo = $this->uploadFile($data['profile_photo'], $path_name);
            } elseif (array_key_exists('profile_photo', $data) && $data['profile_photo'] === null) {
                // Case where photo is explicitly removed (e.g., clear button on form)
                if ($user->profile_photo) {
                    $this->deleteFile($user->profile_photo);
                }
                $user->profile_photo = null;
            }

            // Assign the single role and update is_admin accordingly
            if (isset($data['role'])) {
                $user->syncRoles([$data['role']]);
                $user->is_admin = $this->getIsAdminValueForRole($data['role']);
            } else {
                // If no role is provided, detach all roles and default to 'member' status
                $user->syncRoles([]);
                $user->is_admin = $this->getIsAdminValueForRole('member');
            }

            if ($user->isDirty()) {
                $user->save();
            }

            // save activity in activitylog
            $activity_data['subject'] = $user->refresh();
            $activity_data['event'] = config('constants.ACTIVITY_LOG.UPDATED_EVENT_NAME');
            $activity_data['description'] = sprintf('User(%s) updated User Account(%s).', auth()->user()->name, $user->name);
            saveActivityLog($activity_data);

            DB::commit();
            return $user;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to update user: ' . $e->getMessage(), ['user_id' => $user->id, 'data' => $data, 'exception' => $e]);
            throw $e;
        }
    }

    public function destroy(User $user)
    {
        DB::beginTransaction();
        try {
            // Save activity in activitylog BEFORE deletion if you need user data
            $activity_data['subject'] = $user;
            $activity_data['event'] = config('constants.ACTIVITY_LOG.DELETED_EVENT_NAME');
            $model_type = (class_basename(auth()->user()->getModel()) === config('constants.LABEL_NAME.USER'))
                ? 'User'
                : class_basename(auth()->user()->getModel());
            $activity_data['description'] = sprintf('%s(%s) deleted User (%s).', $model_type, auth()->user()->name, $user->name);
            saveActivityLog($activity_data);

            // Delete profile photo file from storage if it exists
            if ($user->profile_photo) {
                $this->deleteFile($user->profile_photo);
            }

            // Delete associated Trainer or Member record if they exist
            if ($user->trainer) {
                $user->trainer->delete();
            }
            if ($user->member) {
                $user->member->delete();
            }

            // Delete the user record
            $deleted = $this->deleteById($user->id);

            DB::commit();
            return $deleted;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete user: ' . $e->getMessage(), ['user_id' => $user->id, 'exception' => $e]);
            throw $e;
        }
    }

    public function changeStatus(User $user)
    {
        DB::beginTransaction();
        try {
            $user->is_active = !$user->is_active;

            if ($user->isDirty()) {
                $user->save();
            }

            // save activity in activitylog
            $activity_data['subject'] = $user->refresh();
            $activity_data['event'] = config('constants.ACTIVITY_LOG.UPDATED_EVENT_NAME');
            $model_type = (class_basename(auth()->user()->getModel()) === config('constants.LABEL_NAME.USER'))
                ? 'User'
                : class_basename(auth()->user()->getModel());
            $activity_data['description'] = sprintf('%s(%s) updated User Account(%s) status to %s.', $model_type, auth()->user()->name, $user->name, $user->is_active ? 'Active' : 'Inactive');
            saveActivityLog($activity_data);

            DB::commit();
            return $user;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to change user status: ' . $e->getMessage(), ['user_id' => $user->id, 'exception' => $e]);
            throw $e;
        }
    }

    public function getUsersCount($filter = [])
    {
        return User::where($filter)->count();
    }

    public function checkActiveUser(array $data)
    {
        // This method was empty, leaving it as a placeholder for your specific logic.
    }

    public function getUserEloquent()
    {
        return User::query()
            ->where('is_admin', 1)
            ->with(['roles'])->select('users.*');
    }

    public function getClientEloquent()
    {
        return User::query()->where('is_admin', 2)->with(['roles'])->select('users.*');
    }

    public function getSubscriberEloquent()
    {
        return User::query()->where('is_admin', 0)->with(['roles'])->select('users.*');
    }

    public function getClients()
    {
        return User::where('is_admin', 2)->get();
    }

    public function getSubscribers()
    {
        return User::where('is_admin', 0)->get();
    }

    private function uploadFile($file, $path)
    {
        if ($file && $file->isValid()) {
            $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $filePath = Storage::disk('public')->putFileAs($path, $file, $filename);
            if ($filePath) {
                return $filePath;
            }
        }
        return null;
    }

    private function deleteFile($filePath)
    {
        if ($filePath && Storage::disk('public')->exists($filePath)) {
            return Storage::disk('public')->delete($filePath);
        }
        return false;
    }
    private function getIsAdminValueForRole(string $roleName): int
    {
        switch (Str::lower($roleName)) {
            case 'admin':
                return 1;
            case 'trainer':
                return 2;
            default: // For 'member' or any other role
                return 0;
        }
    }
    private function splitFullName(string $fullName): array
    {
        $parts = explode(' ', $fullName, 2);
        return [
            'first_name' => $parts[0],
            'last_name' => $parts[1] ?? null,
        ];
    }
}
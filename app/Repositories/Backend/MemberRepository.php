<?php
namespace App\Repositories\Backend;

use App\Models\Member;
use App\Repositories\BaseRepository;

class MemberRepository extends BaseRepository
{
    public function model()
    {
        return Member::class;
    }

    public function getMembersEloquent()
    {
        return Member::query()
            ->select('members.*')
            ->with(['classRegistrations', 'membershipType'])
            ->orderBy('first_name', 'asc');
    }

    public function getMember($member)
    {
        return $this->getById($member->id);
    }

    public function create(array $data)
    {
        $member = Member::create([
            'user_id' => $data['user_id'] ?? null,
            'membership_type_id' => $data['membership_type_id'],
            'member_id' => $data['member_id'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'date_of_birth' => $data['date_of_birth'],
            'gender' => $data['gender'],
            'address' => $data['address'],
            'emergency_contact_name' => $data['emergency_contact_name'] ?? null,
            'emergency_contact_phone' => $data['emergency_contact_phone'] ?? null,
            'join_date' => $data['join_date'],
            'membership_start_date' => $data['membership_start_date'],
            'membership_end_date' => $data['membership_end_date'],
            'status' => $data['status'],
            'profile_photo' => $data['profile_photo'] ?? null,
            'medical_conditions' => $data['medical_conditions'] ?? null,
            'fitness_goals' => $data['fitness_goals'] ?? null,
            'preferred_workout_time' => $data['preferred_workout_time'] ?? null,
            'referral_source' => $data['referral_source'] ?? null,
            'is_active' => $data['status'] === 'active',
        ]);

        $activity_data['subject'] = $member;
        $activity_data['event'] = config('constants.ACTIVITY_LOG.CREATED_EVENT_NAME');
        $activity_data['description'] = sprintf('Member (%s) was created by %s.', $member->full_name, auth()->user()->name);
        $this->logActivity($activity_data);

        return $member;
    }

    public function update(Member $member, array $data)
    {
        $member->update([
            'user_id' => $data['user_id'] ?? $member->user_id,
            'membership_type_id' => $data['membership_type_id'] ?? $member->membership_type_id,
            'first_name' => $data['first_name'] ?? $member->first_name,
            'last_name' => $data['last_name'] ?? $member->last_name,
            'email' => $data['email'] ?? $member->email,
            'phone' => $data['phone'] ?? $member->phone,
            'date_of_birth' => $data['date_of_birth'] ?? $member->date_of_birth,
            'gender' => $data['gender'] ?? $member->gender,
            'address' => $data['address'] ?? $member->address,
            'emergency_contact_name' => $data['emergency_contact_name'] ?? $member->emergency_contact_name,
            'emergency_contact_phone' => $data['emergency_contact_phone'] ?? $member->emergency_contact_phone,
            'membership_start_date' => $data['membership_start_date'] ?? $member->membership_start_date,
            'membership_end_date' => $data['membership_end_date'] ?? $member->membership_end_date,
            'status' => $data['status'] ?? $member->status,
            'profile_photo' => $data['profile_photo'] ?? $member->profile_photo,
            'medical_conditions' => $data['medical_conditions'] ?? $member->medical_conditions,
            'fitness_goals' => $data['fitness_goals'] ?? $member->fitness_goals,
            'preferred_workout_time' => $data['preferred_workout_time'] ?? $member->preferred_workout_time,
            'referral_source' => $data['referral_source'] ?? $member->referral_source,
            'is_active' => ($data['status'] ?? $member->status) === 'active',
        ]);

        $activity_data['subject'] = $member;
        $activity_data['event'] = config('constants.ACTIVITY_LOG.UPDATED_EVENT_NAME');
        $activity_data['description'] = sprintf('Member (%s) was updated by %s.', $member->full_name, auth()->user()->name);
        $this->logActivity($activity_data);

        return $member;
    }

    public function destroy(Member $member)
    {
        $activity_data['subject'] = $member;
        $activity_data['event'] = config('constants.ACTIVITY_LOG.DELETED_EVENT_NAME');
        $activity_data['description'] = sprintf('Member (%s) was deleted by %s.', $member->full_name, auth()->user()->name);
        $this->logActivity($activity_data);

        return $member->delete();
    }

    public function registerToClass($memberId, $classId)
    {
        $member = $this->getById($memberId);
        if ($member) {
            return $member->classRegistrations()->create([
                'class_id' => $classId,
                'registration_date' => now(),
                'status' => 'Registered'
            ]);
        }
        return false;
    }
}
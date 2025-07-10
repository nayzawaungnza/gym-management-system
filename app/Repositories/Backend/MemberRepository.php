<?php

namespace App\Repositories\Backend;

use App\Models\Member;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Hash;

class MemberRepository extends BaseRepository
{
    public function model()
    {
        return Member::class;
    }

    public function getMembersEloquent()
    {
        return Member::with(['membershipType'])
            ->select('members.*');
    }

    public function create(array $data)
    {
        $member = Member::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'join_date' => $data['join_date'],
            'membership_type_id' => $data['membership_type_id'],
            'status' => $data['status'] ?? 'Active'
        ]);

        // Save activity log
        $activity_data['subject'] = $member;
        $activity_data['event'] = config('constants.ACTIVITY_LOG.CREATED_EVENT_NAME');
        $activity_data['description'] = sprintf('Member (%s) was created.', $member->full_name);
        saveActivityLog($activity_data);

        return $member;
    }

    public function update(Member $member, array $data)
    {
        $member->update([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'membership_type_id' => $data['membership_type_id'],
            'status' => $data['status']
        ]);

        // Save activity log
        $activity_data['subject'] = $member->refresh();
        $activity_data['event'] = config('constants.ACTIVITY_LOG.UPDATED_EVENT_NAME');
        $activity_data['description'] = sprintf('Member (%s) was updated.', $member->full_name);
        saveActivityLog($activity_data);

        return $member;
    }

    public function destroy(Member $member)
    {
        $deleted = $member->delete();

        if ($deleted) {
            // Save activity log
            $activity_data['subject'] = $member;
            $activity_data['event'] = config('constants.ACTIVITY_LOG.DELETED_EVENT_NAME');
            $activity_data['description'] = sprintf('Member (%s) was deleted.', $member->full_name);
            saveActivityLog($activity_data);
        }

        return $deleted;
    }

    public function changeStatus(Member $member, string $status)
    {
        $member->update(['status' => $status]);

        // Save activity log
        $activity_data['subject'] = $member->refresh();
        $activity_data['event'] = config('constants.ACTIVITY_LOG.UPDATED_EVENT_NAME');
        $activity_data['description'] = sprintf('Member (%s) status changed to %s.', $member->full_name, $status);
        saveActivityLog($activity_data);

        return $member;
    }
}

<?php

namespace App\Services\Interfaces;

use App\Models\Member;
use Illuminate\Http\Request;

interface MemberServiceInterface
{
    public function getMembers(Request $request);
    public function getMember($id);
    public function createMember(array $data);
    public function updateMember(Member $member, array $data);
    public function deleteMember(Member $member);
    public function changeMemberStatus(Member $member, string $status);
}

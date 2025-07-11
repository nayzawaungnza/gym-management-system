<?php

namespace App\Services\Interfaces;

use App\Models\Member;

interface MemberServiceInterface
{
    public function getMembersEloquent();
    public function getMember(Member $member);
    public function createMember(array $data);
    public function updateMember(Member $member, array $data);
    public function deleteMember(Member $member);
    public function registerMemberToClass($memberId, $classId);
}
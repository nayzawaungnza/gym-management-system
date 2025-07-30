<?php

namespace App\Services\Interfaces;

use App\Models\Member;
use Illuminate\Database\Eloquent\Builder;

interface MemberServiceInterface
{
    public function getMembersEloquent(): Builder;
    public function getMember(Member $member): Member;
    public function createMember(array $data): Member;
    public function updateMember(Member $member, array $data): bool;
    public function deleteMember(Member $member): bool;
    public function registerMemberToClass($memberId, $classId);
}
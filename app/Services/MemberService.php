<?php

namespace App\Services;

use App\Models\Member;
use App\Repositories\Backend\MemberRepository;
use App\Services\Interfaces\MemberServiceInterface;
use Illuminate\Http\Request;

class MemberService implements MemberServiceInterface
{
    protected $memberRepository;

    public function __construct(MemberRepository $memberRepository)
    {
        $this->memberRepository = $memberRepository;
    }

    public function getMembers(Request $request)
    {
        return $this->memberRepository->getMembersEloquent();
    }

    public function getMember($id)
    {
        return $this->memberRepository->getById($id);
    }

    public function createMember(array $data)
    {
        return $this->memberRepository->create($data);
    }

    public function updateMember(Member $member, array $data)
    {
        return $this->memberRepository->update($member, $data);
    }

    public function deleteMember(Member $member)
    {
        return $this->memberRepository->destroy($member);
    }

    public function changeMemberStatus(Member $member, string $status)
    {
        return $this->memberRepository->changeStatus($member, $status);
    }
}

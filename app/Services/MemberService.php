<?php

namespace App\Services;

use App\Models\Member;
use App\Repositories\Backend\MemberRepository;
use App\Services\Interfaces\MemberServiceInterface;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class MemberService implements MemberServiceInterface
{
    protected $memberRepository;

    public function __construct(MemberRepository $memberRepository)
    {
        $this->memberRepository = $memberRepository;
    }

    public function getMember(Member $member)
    {
        return $this->memberRepository->getMember($member);
    }

    public function getMembersEloquent()
    {
        return $this->memberRepository->getMembersEloquent()
            ->with(['membershipType'])
            ->orderBy('first_name', 'asc');
    }

    /**
     * Creates a new member with a database transaction and error handling.
     *
     * @param array $data
     * @return Member
     * @throws InvalidArgumentException
     */
    public function createMember(array $data)
    {
        DB::beginTransaction();
        try {
            $data['join_date'] = now();
            $data['member_id'] = $this->generateMemberId();
            $member = $this->memberRepository->create($data);
        } catch (Exception $exc) {
            DB::rollBack();
            Log::error('Member Creation Error: ' . $exc->getMessage());
            throw new InvalidArgumentException('Unable to create Member');
        }
        DB::commit();

        return $member;
    }

    /**
     * Updates a member with a database transaction and error handling.
     *
     * @param Member $member
     * @param array $data
     * @return bool
     * @throws InvalidArgumentException
     */
    public function updateMember(Member $member, array $data)
    {
        DB::beginTransaction();
        try {
            $result = $this->memberRepository->update($member, $data);
        } catch (Exception $exc) {
            DB::rollBack();
            Log::error('Member Update Error: ' . $exc->getMessage());
            throw new InvalidArgumentException('Unable to update Member');
        }
        DB::commit();

        return $result;
    }

    /**
     * Deletes a member with a database transaction and error handling.
     *
     * @param Member $member
     * @return bool
     * @throws InvalidArgumentException
     */
    public function deleteMember(Member $member)
    {
        DB::beginTransaction();
        try {
            $result = $this->memberRepository->destroy($member);
        } catch (Exception $exc) {
            DB::rollBack();
            Log::error('Member Deletion Error: ' . $exc->getMessage());
            throw new InvalidArgumentException('Unable to delete Member');
        }
        DB::commit();

        return $result;
    }

    public function registerMemberToClass($memberId, $classId)
    {
         DB::beginTransaction();
        try {
            $result = $this->memberRepository->registerToClass($memberId, $classId);
        } catch (Exception $exc) {
            DB::rollBack();
            Log::error('Member registration Error: ' . $exc->getMessage());
            throw new InvalidArgumentException('Unable to register Member');
        }
        DB::commit();
        return $result;
    }

    protected function generateMemberId()
    {
        return 'MEM' . date('Ymd') . strtoupper(substr(uniqid(), -5));
    }
}
<?php

namespace App\Http\Controllers\Api\Chat;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Message;
use App\Models\Participant;
use App\Models\User;
use App\Traits\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GroupAdminManageController extends Controller
{
    use ApiResponse;

    /**
     * Promote a member to admin in a group.
     *
     * @param Request $request
     * @param int $group_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function promoteAsAdmin(Request $request, int $group_id)
    {
        $user = auth()->user();

        if (!$user) {
            return $this->error([], 'Unauthorized', 401);
        }

        $group = Group::find($group_id);

        if (!$group) {
            return $this->error([], 'Group not found', 404);
        }

        $isSuperAdmin = Participant::where('conversation_id', $group->conversation_id)
            ->where('participant_id', $user->id)
            ->whereIn('role', ['super_admin'])
            ->exists();

        if (!$isSuperAdmin) {
            return $this->error([], 'Forbidden: Only group super_admin can promote as leader', 403);
        }

        $validator = Validator::make($request->all(), [
            'member_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), $validator->errors()->first(), 422);
        }

        $memberId = $request->member_id;

        // Prevent leader from removing himself
        if ($memberId == $user->id) {
            return $this->error([], 'You can not promote you as admin', 422);
        }

        // Check if the member exists in the group
        $participant = Participant::where('conversation_id', $group->conversation_id)
            ->where('participant_id', $memberId)
            ->first();

        if (!$participant) {
            return $this->error([], 'Member not found in the group', 404);
        }

        $participant->role = 'admin';
        $participant->save();

        $member = User::find($memberId);
        Message::create([
            'sender_id' => $user->id,
            'conversation_id' => $group->conversation_id,
            'message' => $user->name . ' promote ' . $member->name . ' as admin',
            'message_type' => 'system',
            'created_at' => Carbon::now(),
        ]);

        return $this->success([], 'Member promoted to admin successfully', 200);
    }

    /**
     * Demote an admin to member in a group.
     *
     * @param Request $request
     * @param int $group_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function demoteAsMember(Request $request, int $group_id)
    {
        $user = auth()->user();

        if (!$user) {
            return $this->error([], 'Unauthorized', 401);
        }

        $group = Group::find($group_id);

        if (!$group) {
            return $this->error([], 'Group not found', 404);
        }

        $isSuperAdmin = Participant::where('conversation_id', $group->conversation_id)
            ->where('participant_id', $user->id)
            ->whereIn('role', ['super_admin'])
            ->exists();

        if (!$isSuperAdmin) {
            return $this->error([], 'Forbidden: Only group super_admin can promote as leader', 403);
        }

        $validator = Validator::make($request->all(), [
            'member_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), $validator->errors()->first(), 422);
        }

        $memberId = $request->member_id;

        // Prevent leader from removing himself
        if ($memberId == $user->id) {
            return $this->error([], 'You can not change your role', 422);
        }

        $participant = Participant::where('conversation_id', $group->conversation_id)
            ->where('participant_id', $memberId)
            ->first();

        if (!$participant) {
            return $this->error([], 'Member not found in the group', 404);
        }

        $participant->role = 'member';
        $participant->save();

        $member = User::find($memberId);
        Message::create([
            'sender_id' => $user->id,
            'conversation_id' => $group->conversation_id,
            'message' => $user->name . ' demoted ' . $member->name . ' as member',
            'message_type' => 'system',
            'created_at' => Carbon::now(),
        ]);

        return $this->success([], 'Member demoted to member successfully', 200);
    }
}

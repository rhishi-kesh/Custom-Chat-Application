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

class GroupParticipantManageController extends Controller
{
    use ApiResponse;
    /**
     * Add members to a group.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function addParticipate(Request $request, int $id)
    {
        $user = auth()->user();

        if (!$user) {
            return $this->error([], 'Unauthorized', 401);
        }

        $group = Group::find($id);

        if (!$group) {
            return $this->error([], 'Group not found', 404);
        }

        // Check if the user has permission to add members
        if($group->allow_members_to_add_remove_participants == 0) {
            // Check if the authenticated user is a leader (admin or super_admin)
            $isLeader = Participant::where('conversation_id', $group->conversation_id)
                ->where('participant_id', $user->id)
                ->whereIn('role', ['admin', 'super_admin'])
                ->exists();

            if (!$isLeader) {
                return $this->error([], 'Forbidden: Only group leaders can add members', 403);
            }
        } else {
            // Check if the authenticated user is a participant of the group
            $isParticipant = Participant::where('conversation_id', $group->conversation_id)
                ->where('participant_id', $user->id)
                ->exists();

            if (!$isParticipant) {
                return $this->error([], 'Forbidden: Only group participants can add members', 403);
            }
        }

        // Validate request
        $validator = Validator::make($request->all(), [
            'member_ids'   => 'required|array|min:1',
            'member_ids.*' => 'exists:users,id',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), $validator->errors()->first(), 422);
        }

        // Remove duplicate IDs in the request
        $memberIds = array_unique($request->member_ids);

        $added = [];
        $skipped = [];

        foreach ($memberIds as $memberId) {

            // Prevent leader from adding himself
            if ($memberId == $user->id) {
                $skipped[] = [
                    'member_id' => $memberId,
                    'reason'    => 'Cannot add yourself',
                ];
                continue;
            }

            // Create participant if not exists
            $participant = Participant::firstOrCreate(
                [
                    'conversation_id' => $group->conversation_id,
                    'participant_id'   => $memberId,
                ],
                [
                    'role' => 'member',
                    'participant_type' => User::class,
                    'joined_at' => Carbon::now(),
                ]
            );

            if ($participant->wasRecentlyCreated) {
                $added[] = $memberId;

                $member = User::find($memberId);
                Message::create([
                    'sender_id' => $user->id,
                    'conversation_id' => $group->conversation_id,
                    'message' => $user->name . ' added ' . $member->name . ' to the conversation',
                    'message_type' => 'system',
                    'created_at' => Carbon::now(),
                ]);
            } else {
                $skipped[] = [
                    'member_id' => $memberId,
                    'reason'    => 'Already a member',
                ];
            }
        }

        return $this->success([
            'added'   => $added,
            'skipped' => $skipped,
        ], 'Members added successfully', 200);
    }


    /**
     * Remove a member from a group.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeParticipate(Request $request, int $id)
    {
        $user = auth()->user();

        if (!$user) {
            return $this->error([], 'Unauthorized', 401);
        }

        $group = Group::find($id);

        if (!$group) {
            return $this->error([], 'Group not found', 404);
        }

        // Check if the user has permission to remove members
        if($group->allow_members_to_add_remove_participants == 0) {
            // Check if the authenticated user is a leader (admin or super_admin)
            $isLeader = Participant::where('conversation_id', $group->conversation_id)
                ->where('participant_id', $user->id)
                ->whereIn('role', ['admin', 'super_admin'])
                ->exists();

            if (!$isLeader) {
                return $this->error([], 'Forbidden: Only group leaders can remove members', 403);
            }
        } else {
            // Check if the authenticated user is a participant of the group
            $isParticipant = Participant::where('conversation_id', $group->conversation_id)
                ->where('participant_id', $user->id)
                ->exists();

            if (!$isParticipant) {
                return $this->error([], 'Forbidden: Only group participants can remove members', 403);
            }
        }

        // Validate request
        $validator = Validator::make($request->all(), [
            'member_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), $validator->errors()->first(), 422);
        }

        $memberId = $request->member_id;

        // Prevent leader from removing himself
        if ($memberId == $user->id) {
            return $this->error([], 'Cannot remove yourself', 422);
        }

        // Check if the member exists in the group
        $participant = Participant::where('conversation_id', $group->conversation_id)
            ->where('participant_id', $memberId)
            ->first();

        if (!$participant) {
            return $this->error([], 'Member not found in the group', 404);
        }

        $member = User::find($memberId);
        Message::create([
            'sender_id' => $user->id,
            'conversation_id' => $group->conversation_id,
            'message' => $user->name . ' remove ' . $member->name . ' from the conversation',
            'message_type' => 'system',
            'created_at' => Carbon::now(),
        ]);

        // Remove participant
        $participant->delete();

        return $this->success([], 'Member removed successfully', 200);
    }

    /**
     * Leave the group.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function leaveGroup(Request $request, int $id)
    {
        $user = auth()->user();

        if (!$user) {
            return $this->error([], 'Unauthorized', 401);
        }

        $group = Group::find($id);

        if (!$group) {
            return $this->error([], 'Group not found', 404);
        }

        // Check if the user is a participant in the group
        $participant = Participant::where('conversation_id', $group->conversation_id)
            ->where('participant_id', $user->id)
            ->first();

        if (!$participant) {
            return $this->error([], 'You are not a member of this group', 403);
        }

        // Prevent super_admin from leaving
        if ($participant->role === 'super_admin') {
            return $this->error([], 'Super admins cannot leave the group', 403);
        }

        Message::create([
            'sender_id' => $user->id,
            'conversation_id' => $group->conversation_id,
            'message' => $user->name . ' has left the conversation',
            'message_type' => 'system',
            'created_at' => Carbon::now(),
        ]);

        // Remove participant
        $participant->delete();

        return $this->success([], 'You have left the group successfully', 200);
    }
}

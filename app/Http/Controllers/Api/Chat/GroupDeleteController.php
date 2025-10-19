<?php

namespace App\Http\Controllers\Api\Chat;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Group;
use App\Models\Message;
use App\Models\Participant;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class GroupDeleteController extends Controller
{
    use ApiResponse;
    /**
     * Delete the group.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Request $request, int $id)
    {
        $user = auth()->user();

        if (!$user) {
            return $this->error([], 'Unauthorized', 401);
        }

        $group = Group::find($id);

        if (!$group) {
            return $this->error([], 'Group not found', 404);
        }

        // Check if the authenticated user is a super_admin of this group
        $isSuperAdmin = Participant::where('conversation_id', $group->conversation_id)
            ->where('participant_id', $user->id)
            ->where('role', 'super_admin')
            ->exists();

        if (!$isSuperAdmin) {
            return $this->error([], 'Forbidden: Only super admins can delete the group', 403);
        }

        DB::transaction(function () use ($group) {

            // 1. Delete group avatar if exists
            if ($group->avatar) {
                $avatarPath = str_replace('storage/', '', $group->avatar);
                if (Storage::disk('public')->exists($avatarPath)) {
                    Storage::disk('public')->delete($avatarPath);
                }
            }

            $conversation_id = $group->conversation_id;

            $messages = Message::with('attachments')->where('conversation_id', $conversation_id)->get();

            foreach ($messages as $message) {
                foreach ($message->attachments as $attachment) {
                    if ($attachment->file_path && Storage::disk('public')->exists($attachment->file_path)) {
                        Storage::disk('public')->delete($attachment->file_path);
                    }
                }
            }
            Message::where('conversation_id', $conversation_id)->delete();
            
            // 1. Delete the group (breaks FK constraint)
            $group->delete();

            // 2. Delete the conversation
            Conversation::where('id', $conversation_id)->delete();
        });

        return $this->success([], 'Group deleted successfully', 200);
    }
}

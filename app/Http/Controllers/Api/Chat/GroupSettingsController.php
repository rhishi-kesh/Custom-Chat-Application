<?php

namespace App\Http\Controllers\Api\Chat;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GroupSettingsController extends Controller
{
    use ApiResponse;
    /**
     * Handle the incoming request to get group info.
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

        $validator = Validator::make($request->all(), [
            'key' => 'required|string|max:255|in:allow_members_to_send_messages,admins_must_approve_new_members,allow_members_to_change_group_info,allow_members_to_add_remove_participants',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), $validator->errors()->first(), 422);
        }

        $key = $request->key ?? null;

        $group = Group::query()
            ->whereHas('conversation.participants', function ($query) use ($user) {
                $query->where('participant_id', $user->id)->whereIn('role', ['super_admin', 'admin']);
            })
            ->find($id);


        if (!$group) {
            return $this->error([], 'Group not found or unauthorized access.', 404);
        }

        $group->$key = !$group->$key;
        $group->save();

        return $this->success($group, 'Group setting updated successfully', 200);
    }
}

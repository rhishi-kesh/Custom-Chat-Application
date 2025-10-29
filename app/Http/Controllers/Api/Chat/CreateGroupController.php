<?php

namespace App\Http\Controllers\Api\Chat;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CreateGroupController extends Controller
{
    use ApiResponse;

    /**
     * Handle the incoming request to create a group.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'members' => 'required|array',
            'members.*' => 'exists:users,id',
            'type' => 'nullable|in:public,private',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), $validator->errors()->first(), 422);
        }

        $user = auth()->user();
        if (!$user) {
            return $this->error([], 'Unauthorized', 401);
        }

        // Add participants (authenticated user + selected members)
        $participantData = collect($request->members)
            ->filter(fn($memberId) => $memberId != $user->id) // avoid duplicate
            ->map(fn($id) => [
                'participant_id' => $id,
                'participant_type' => get_class($user),
                'role' => 'member',
            ])
            ->toArray();

        $participantData[] = [
            'participant_id' => $user->id,
            'participant_type' => get_class($user),
            'role' => 'super_admin',
        ];

        if ((empty($participantData) || count($participantData) < 3) && count($request->members) >= 100) {
            return $this->error([], 'At least three member is required', 422);
        }

        // Create the conversation
        $conversation = Conversation::create([
            'type' => 'group',
        ]);

        $conversation->participants()->createMany($participantData);

        // Create system message
        $conversation->messages()->create([
            'sender_id' => $user->id,
            'message' => 'Group created',
            'message_type' => 'system',
        ]);

        // Save avatar if provided
        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $avatarPath = 'storage/' . $request->file('avatar')->store('group/avatars', 'public');
        }

        // Create group info
        $group = $conversation->group()->create([
            'name' => $request->input('name'),
            'avatar' => $avatarPath,
            'type' => $request->input('type', 'private'),
        ]);

        return $this->success([
            'conversation_id' => $conversation->id,
            'group' => $group,
            'participants' => $conversation->participants()->with('participant:id,name,avatar')->get(),
        ], 'Group created successfully');
    }
}

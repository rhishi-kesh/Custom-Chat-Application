<?php

namespace App\Http\Controllers\Api\Chat;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GetMessageController extends Controller
{
    use ApiResponse;

    /**
     * Get chat messages based on receiver ID or conversation ID.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getChat(Request $request)
    {
        $validator = Validator::make($request->query(), [
            'receiver_id' => ['nullable', 'required_without:conversation_id', 'integer'],
            'conversation_id' => ['nullable', 'required_without:receiver_id', 'integer'],
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), $validator->errors()->first(), 422);
        }

        $user = auth()->user();
        if (!$user) {
            return $this->error([], 'Unauthorized', 401);
        }

        $receiver_id = null;
        if ($request->query('receiver_id')) {
            $receiver = User::find($request->query('receiver_id'));
            if (!$receiver) {
                return $this->error([], 'Receiver not found', 404);
            }
            $receiver_id = $receiver->id;
        }

        $conversation_id = $request->query('conversation_id');

        // Conversation logic
        $conversation = $this->getConversation($user, $receiver_id, $conversation_id);
        return $conversation;
    }

    /**
     * Get or create a conversation based on the provided parameters.
     *
     * @param User $user
     * @param int|null $receiver_id
     * @param int|null $conversation_id
     * @return Conversation|null
     */
    private function getConversation(User $user, $receiver_id = null, $conversation_id = null)
    {
        if ($conversation_id) {
            $conversation = Conversation::where('id', $conversation_id)
                ->whereHas('participants', function ($query) use ($user) {
                    $query->where('participant_id', $user->id)
                        ->where('participant_type', User::class);
                })
                ->where('type', 'group')
                ->first();

            if (!$conversation) {
                return $this->error([], 'Conversation not found', 404);
            } else {
                return $conversation;
            }
        } elseif ($receiver_id) {
            $receiver = User::find($receiver_id);
            if (!$receiver) {
                return $this->error([], 'Receiver not found', 404);
            }

            if ($receiver->id === $user->id) {
                $conversation = Conversation::whereHas('participants', function ($q) use ($user) {
                    $q->where('participant_id', $user->id)
                        ->where('participant_type', User::class);
                })
                    ->where('type', 'self')
                    ->first();

                if (!$conversation) {
                    $conversation = Conversation::create([
                        'type' => 'self',
                    ]);

                    $conversation->participants()->createMany([
                        [
                            'participant_id' => $user->id,
                            'participant_type' => User::class,
                        ],
                    ]);
                    return $conversation;
                } else {
                    return $conversation;
                }
            }

            $conversation = Conversation::whereHas('participants', function ($q) use ($user) {
                $q->where('participant_id', $user->id)
                    ->where('participant_type', User::class);
            })
                ->whereHas('participants', function ($q) use ($receiver) {
                    $q->where('participant_id', $receiver->id)
                        ->where('participant_type', User::class);
                })
                ->where('type', 'private')
                ->first();

            if (!$conversation) {
                $conversation = Conversation::create([
                    'type' => 'private',
                ]);

                $conversation->participants()->createMany([
                    [
                        'participant_id' => $receiver->id,
                        'participant_type' => User::class,
                    ],
                    [
                        'participant_id' => $user->id,
                        'participant_type' => User::class,
                    ],
                ]);
                return $conversation;
            } else {
                return $conversation;
            }
        } else {
            return $this->error([], 'Either receiver_id or conversation_id is required', 422);
        }
    }
}

<?php

namespace App\Http\Controllers\Api\Chat;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\MessageReaction;
use App\Models\Participant;
use App\Models\UserMessageReact;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ReactMessageController extends Controller
{
    use ApiResponse;

    /**
     * React to a message (add or update reaction).
     * @param int $message_id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function reactSend(int $message_id, Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return $this->error([], 'Unauthorized', 401);
        }

        $message = Message::find($message_id);
        if (!$message) {
            return $this->error([], 'Message not found', 404);
        }

        // Check if user belongs to the conversation
        $isParticipant = Participant::where('conversation_id', $message->conversation_id)
            ->where('participant_id', $user->id)
            ->exists();

        if (!$isParticipant) {
            return $this->error([], 'You are not a participant in this conversation', 403);
        }

        // Validate request
        $validator = Validator::make($request->all(), [
            'emoji' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), $validator->errors()->first(), 422);
        }

        $newEmoji = $request->emoji;

        // Find if the user already reacted to this message
        $userReaction = UserMessageReact::where('message_id', $message->id)
            ->where('user_id', $user->id)
            ->first();

        // If user already reacted → change emoji
        if ($userReaction) {
            $oldEmoji = $userReaction->emoji;

            if ($oldEmoji === $newEmoji) {
                return $this->success([], 'Reaction updated', 200);
            }

            // Decrease old reaction count
            $oldReaction = MessageReaction::where('message_id', $message->id)
                ->where('emoji', $oldEmoji)
                ->first();

            if ($oldReaction) {
                $oldReaction->count -= 1;
                if ($oldReaction->count <= 0) {
                    $oldReaction->delete();
                } else {
                    $oldReaction->save();
                }
            }

            // Increase new reaction count atomically
            MessageReaction::updateOrCreate(
                ['message_id' => $message->id, 'emoji' => $newEmoji],
                ['count' => DB::raw('count + 1')]
            );

            // Update user reaction
            $userReaction->emoji = $newEmoji;
            $userReaction->save();

            return $this->success([], 'Reaction updated successfully', 200);
        }

        // =========================
        // NO PREVIOUS USER REACTION
        // =========================

        // Create user reaction
        UserMessageReact::create([
            'message_id' => $message->id,
            'user_id'    => $user->id,
            'emoji'      => $newEmoji,
        ]);

        // Increase emoji count
        $messageReaction = MessageReaction::firstOrCreate(
            ['message_id' => $message->id, 'emoji' => $newEmoji],
            ['count' => 0]
        );

        $messageReaction->increment('count');

        return $this->success([], 'Reaction added successfully', 200);
    }


    /**
     * Remove reaction from a message.
     * @param int $message_id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function reactRemove(int $message_id, Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return $this->error([], 'Unauthorized', 401);
        }

        $message = Message::find($message_id);
        if (!$message) {
            return $this->error([], 'Message not found', 404);
        }

        // Check if user belongs to the conversation
        $isParticipant = Participant::where('conversation_id', $message->conversation_id)
            ->where('participant_id', $user->id)
            ->exists();

        if (!$isParticipant) {
            return $this->error([], 'You are not a participant in this conversation', 403);
        }

        // Find user's reaction (THIS table!)
        $userReaction = UserMessageReact::where('message_id', $message->id)
            ->where('user_id', $user->id)
            ->first();

        if (!$userReaction) {
            return $this->error([], 'Reaction not found', 404);
        }

        $emoji = $userReaction->emoji;

        // Remove user reaction
        $userReaction->delete();

        // Decrease the emoji count
        $messageReaction = MessageReaction::where('message_id', $message->id)
            ->where('emoji', $emoji)
            ->first();

        if ($messageReaction) {
            $messageReaction->count -= 1;

            if ($messageReaction->count <= 0) {
                $messageReaction->delete();
            } else {
                $messageReaction->save();
            }
        }

        return $this->success([], 'Reaction removed successfully', 200);
    }
}

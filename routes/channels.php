<?php

use App\Models\Conversation;
use Illuminate\Support\Facades\Broadcast;

// Channel for individual users
Broadcast::channel('chat-channel.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

// Channel for chat conversations
Broadcast::channel('conversation-channel.{conversationId}', function ($user, $conversationId) {
    return Conversation::where('id', $conversationId)
        ->whereHas('participants', function ($q) use ($user) {
            $q->where('participant_id', $user->id);
        })
        ->exists();
});

// Channel for chat messages
Broadcast::channel('chat-channel.{conversationId}', function ($user, $conversationId) {
    return Conversation::where('id', $conversationId)
        ->whereHas('participants', function ($q) use ($user) {
            $q->where('participant_id', $user->id);
        })
        ->exists();
});

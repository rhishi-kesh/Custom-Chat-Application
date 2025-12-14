<?php

use App\Models\Conversation;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| ConversationEvent
|--------------------------------------------------------------------------
| Channel: conversation-channel.{participantId}
| Purpose: Notify a specific participant (user-based)
*/

Broadcast::channel('conversation-channel.{participantId}', function ($user, $participantId) {
    return (int) $user->id === (int) $participantId;
});


/*
|--------------------------------------------------------------------------
| MessageSentEvent
|--------------------------------------------------------------------------
| Channel: chat-channel.{conversationId}
| Purpose: Broadcast messages inside a conversation
*/
Broadcast::channel('chat-channel.{conversationId}', function ($user, $conversationId) {
    return Conversation::where('id', $conversationId)
        ->whereHas('participants', function ($q) use ($user) {
            $q->where('participant_id', $user->id);
        })
        ->exists();
});

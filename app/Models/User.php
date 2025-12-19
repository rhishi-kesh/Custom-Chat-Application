<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'provider',
        'provider_id',
        'agree_to_terms',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    // Messages sent by this user
    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    // Messages received by this user (for direct/private chats only)
    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    // Conversations where user is a participant
    public function participants()
    {
        return $this->morphMany(Participant::class, 'participant');
    }

    public function conversations()
    {
        return $this->hasManyThrough(
            Conversation::class,
            Participant::class,
            'participant_id', // Foreign key on Participant table
            'id',             // Local key on Conversation table
            'id',             // Local key on User table
            'conversation_id' // Foreign key on Participant table
        )->where('participant_type', self::class);
    }

    // Message reactions made by this user
    public function messageReactions()
    {
        return $this->hasMany(MessageReaction::class);
    }

    // Read/delivery status records
    public function messageStatuses()
    {
        return $this->hasMany(MessageStatus::class);
    }

    public function firebaseTokens()
    {
        return $this->hasOne(FirebaseToken::class);
    }
}

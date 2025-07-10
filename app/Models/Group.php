<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $guarded = [];

    protected $hidden = [
        'updated_at',
    ];

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'conversation_id' => 'integer',
        ];
    }

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }
}

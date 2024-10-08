<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        "conversation_id",
        "user_id",
        "content",
        "read_at"
    ];

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function seen_by(){
        return $this->hasMany(MessagesSeenBy::class, 'message_id')
            ->selectRaw('DISTINCT user_id')
            ->with('user');
    }

    public function deleted_by(){
        return $this->hasMany(MessagesDeletedBy::class, 'message_id');
    }

    public function images(){
        return $this->hasMany(MessageImage::class, 'message_id');
    }

}

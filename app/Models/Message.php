<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Message extends Model {
    use HasFactory;

    protected $fillable = [
        'content', 
        'user_id',
        'conversation_id',
        'timestamp',
        'parent_message_id',
    ];

    public function conversation(){
        return $this->belongsTo(Conversation::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function replies(){
        return $this->hasMany(Message::class, 'parent_message_id');
    }

    public function reactions(){
        return $this->hasMany(MessageReaction::class);
    }

    public function attachments(){
        return $this->hasMany(Attachment::class);
    }
}
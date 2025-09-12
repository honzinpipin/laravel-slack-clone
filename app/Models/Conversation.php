<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Conversation extends Model{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
    ];

    public function users(){
        return $this->belongsToMany(User::class, 'conversation_user');
    }

    public function messages(){
        return $this->hasMany(Message::class);
    }
}
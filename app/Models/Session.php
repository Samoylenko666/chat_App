<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Session extends Model
{
    use HasFactory;
    protected $fillable=[
        'user1_id',
        'user2_id',
    ];
    public function chats(){
        return $this->hasManyThrough(Chat::class,Message::class);
    }

    public function messages(){
        return $this->hasMany(Message::class);
    }

    public function deleteChats(){
        $this->chats()->where("user_id",Auth()->id())->delete();
    }
    public function deleteMessages(){
        $this->messages()->delete();
    }
}

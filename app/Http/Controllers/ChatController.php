<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Session;
use App\Http\Resources\ChatResource;
use App\Events\PrivateChatEvent;   

use Carbon\Carbon;

class ChatController extends Controller
{
    //

    public function send(Request $request, Session $session)
    {
       $message=$session->messages()->create([
           'content'        => $request->get('content')
           ]);
          $chat=$message->createForSend($session->id); 
           $message->createForReceive($session->id,$request->get('to_user'));
           broadcast(new PrivateChatEvent($message->content,$chat));
           return response($message,200);
    }



    public function getChats(Request $request, Session $session){
        return ChatResource::collection ($session->chats->where('user_id',Auth()->id()));

    }
    public function read(Session $session){

       $chats= $session->chats->where('read_at',null)->where('type',0)
        ->where("user_id","!=",Auth()->id());
        foreach($chats as $chat){
            $chat->update(['read_at'=>Carbon::now()]);

        }


    }



}
//PUSHER_APP_ID=1170252
//PUSHER_APP_KEY=aae650e75c27f7ca7039
//PUSHER_APP_SECRET=0f6ec4a546a752f5938e
//PUSHER_APP_CLUSTER=ap1

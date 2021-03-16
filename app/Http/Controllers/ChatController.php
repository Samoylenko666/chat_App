<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Session;
use App\Http\Resources\ChatResource;
use App\Events\PrivateChatEvent;   
use App\Events\MsgReadEvent; 

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
           return response($chat->id,200);
    }



    public function getChats(Request $request, Session $session){
        return ChatResource::collection ($session->chats->where('user_id',Auth()->id()));

    }
    public function read(Session $session){

       $chats= $session->chats->where('read_at',null)->where('type',0)
        ->where("user_id","!=",Auth()->id());
        foreach($chats as $chat){
            $chat->update(['read_at'=>Carbon::now()]);
            broadcast (new MsgReadEvent(new ChatResource ($chat),$chat->session_id));
        }


    }
    public function clear(Session $session){
       $session->deleteChats();
        //Если для этой сессии нет ни одного чата удаляем все сообщения для этой сессиии
        $session->chats->count() == 0 ? $session->deleteMessages() : '';
        return response('cleared',200);
    
    }


}
//PUSHER_APP_ID=1170252
//PUSHER_APP_KEY=aae650e75c27f7ca7039
//PUSHER_APP_SECRET=0f6ec4a546a752f5938e
//PUSHER_APP_CLUSTER=ap1

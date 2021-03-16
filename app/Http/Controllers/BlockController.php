<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Session;
use App\Events\BlockEvent; 

class BlockController extends Controller
{
    public function block(Session $session){
        $session->block= true;
        $session->blocked_by= Auth()->id();
        $session->save();
        broadcast(new BlockEvent($session->id, true));
        return response('blocked',200);
    }
    
    public function unblock(Session $session){

        $session->block= false;
        $session->blocked_by=null;
        broadcast(new BlockEvent($session->id, false));
        $session->save();
    }
}

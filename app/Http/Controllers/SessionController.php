<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;



use App\Models\Session;
use App\Models\User;
use App\Http\Resources\SessionResource;
use App\Events\SessionEvent;

class SessionController extends Controller
{      use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    //

    public function create(Request $request){
       
      //  return auth()->id();
     // return $request;
        $session=Session::create([
            'user1_id'  =>  Auth()->id(),
            'user2_id'  =>  $request->get('friend_id')
            ]);
      $modifiedSession=new  SessionResource($session);
      broadcast(new SessionEvent($modifiedSession,Auth()->id()));
      return  $modifiedSession;


    }
}

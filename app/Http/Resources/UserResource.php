<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Session;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
       // return parent::toArray($request);
       return [
        'id'        =>    $this->id,   
        'name'      =>    $this->name,
        'email'     =>    $this->email,
        'online'    =>    false,
        'session'   =>    $this->session_details($this->id)// Вызываем функицю из этого котроллера и передаем айди юзера
       ];
    }

    private function session_details($id){
        $session=Session::whereIn('user1_id',[Auth()->id(),$id])->whereIn('user2_id',[Auth()->id(),$id])->first();
        return new SessionResource($session);
    }
}

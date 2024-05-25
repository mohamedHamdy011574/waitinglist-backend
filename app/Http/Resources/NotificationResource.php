<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class NotificationResource extends JsonResource
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
            'id' => $this->id,
            'user_id' => $this->user_id,
            'title' => ucfirst($this->title),
            'message' => ucfirst($this->message),
            'type' => ($this->type) ?  $this->type : "",
            'redirect_to' => ($this->redirect_to) ? $this->redirect_to : "",
            'redirect_id' => ($this->redirect_id) ? $this->redirect_id : 0,
            'status' => $this->status,

            // 'created_at' => $this->created_at,
            // 'time_formatted' => $this->created_at->diffForHumans(),
            
        ];
    }
}

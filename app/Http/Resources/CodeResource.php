<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CodeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'=> $this->id,
            'code' => $this->code,
            'user'=> $this->user,
            'customers'=> $this->customers,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

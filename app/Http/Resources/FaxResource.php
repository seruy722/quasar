<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FaxResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'departure_date' => $this->departure_date,
            'delivered' => $this->delivered,
            'transport_id' => $this->transport_id,
            'transport_name'=>$this->transport->name,
            'transporter_id' => $this->transporter_id,
            'transporter_name'=>$this->transporter->name,
            'user_name' => $this->user->name,
        ];
    }
}

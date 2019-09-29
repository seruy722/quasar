<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SkladResource extends JsonResource
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
            'code' => $this->code,
            'client' => $this->customer->code,
            'place' => $this->place,
            'kg' => $this->kg,
            'shop' => $this->shop,
            'things' => $this->things,
            'city' => $this->city,
            'category' => $this->category->name,
            'notation' => $this->notation,
        ];
    }
}

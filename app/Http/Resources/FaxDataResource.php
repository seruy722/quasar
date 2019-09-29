<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FaxDataResource extends JsonResource
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
            'code' => $this->code,
            'customer_code' => $this->customer->code,
            'code_id' => $this->code_id,
            'fax_id' => $this->fax_id,
            'fax' => $this->fax->name,
            'place' => $this->place,
            'kg' => $this->kg,
            'for_kg' => $this->for_kg,
            'for_place' => $this->for_place,
            'shop' => $this->shop,
            'things' => $this->things,
            'category' => $this->category->name,
            'category_id' => $this->category_id,
            'notation' => $this->notation,
            'brand' => $this->brand,
            'change' => false,
        ];
    }
}

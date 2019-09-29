<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FaxDataCommonExportResource extends JsonResource
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
            'code' => $this->code,
            'customer_code' => $this->codesCode,
            'place' => $this->place,
            'kg' => $this->kg,
            'shop' => $this->shop,
            'category' => $this->category->name,
            'notation' => $this->notation,
            'things' => $this->things,
        ];
    }
}

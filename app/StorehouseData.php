<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StorehouseData extends Model
{
    protected $fillable = ['code_place', 'code_client_id', 'place', 'kg', 'shop', 'things', 'brand', 'notation', 'for_kg', 'for_place', 'fax_id', 'category_id', 'storehouse_id', 'status', 'destroyed', 'delivery_method_id', 'department', 'cube'];

    public function getCreatedAtAttribute($value)
    {
        if ($value) {
            return \Illuminate\Support\Carbon::parse($value)->toAtomString();
        }
        return null;
    }
}

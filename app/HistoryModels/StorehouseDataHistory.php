<?php

namespace App\HistoryModels;

use Illuminate\Database\Eloquent\Model;

class StorehouseDataHistory extends Model
{
    protected $fillable = ['action', 'storehouse_data_id', 'code_place', 'code_client_id', 'place', 'kg', 'shop', 'things', 'brand', 'notation', 'for_kg', 'for_place', 'fax_id', 'category_id', 'storehouse_id', 'status', 'destroyed', 'user_id'];

    public function getCreatedAtAttribute($value)
    {
        if ($value) {
            return \Illuminate\Support\Carbon::parse($value)->toAtomString();
        }
        return null;
    }
}

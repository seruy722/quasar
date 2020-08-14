<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cargo extends Model
{
    protected $fillable = ['code_place', 'code_client_id', 'type', 'place', 'kg', 'shop', 'things', 'brand', 'notation', 'for_kg', 'for_place', 'fax_id', 'category_id', 'storehouse_id', 'delivery_method_id', 'department', 'paid', 'sum', 'created_at', 'sale', 'get_pay_user_id'];

    public function getCreatedAtAttribute($value)
    {
        if ($value) {
            return \Illuminate\Support\Carbon::parse($value)->toAtomString();
        }
        return null;
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StorehouseHistory extends Model
{
    protected $fillable = ['storehouse_entry_id', 'code_place', 'code_client_id', 'place', 'kg', 'category_id'];

    public function getCreatedAtAttribute($value)
    {
        if ($value) {
            return \Illuminate\Support\Carbon::parse($value)->toAtomString();
        }
        return null;
    }
}

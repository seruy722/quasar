<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CodesPrices extends Model
{
    protected $table = 'code_prices';
    protected $fillable = ['code_id', 'category_id', 'for_kg', 'for_place', 'commission'];

    public function getCreatedAtAttribute($value)
    {
        if ($value) {
            return \Illuminate\Support\Carbon::parse($value)->toAtomString();
        }
        return null;
    }

    public function getUpdatedAtAttribute($value)
    {
        if ($value) {
            return \Illuminate\Support\Carbon::parse($value)->toAtomString();
        }
        return null;
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CodePrice extends Model
{
    protected $fillable = ['code_id', 'category_id', 'for_kg', 'for_place'];

    public function getCreatedAtAttribute($value)
    {
        if ($value) {
            return \Illuminate\Support\Carbon::parse($value)->toAtomString();
        }
        return null;
    }
}

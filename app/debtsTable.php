<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class debtsTable extends Model
{
    protected $fillable = ['type', 'code_id', 'sum', 'notation', 'created_at'];

    public function getCreatedAtAttribute($value)
    {
        return \Illuminate\Support\Carbon::parse($value)->format('d.m.Y');
//        return \Illuminate\Support\Carbon::parse($value)->toAtomString();
    }


    public function getCodeIdAttribute($value)
    {
        return Code::find($value)->code;
    }
}

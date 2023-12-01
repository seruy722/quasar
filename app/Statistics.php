<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Statistics extends Model
{
    protected $fillable = ['expense_id', 'sum', 'created_at', 'description'];

    public function getCreatedAtAttribute($value)
    {
        if ($value) {
            return \Illuminate\Support\Carbon::parse($value)->toAtomString();
        }
        return null;
    }
}

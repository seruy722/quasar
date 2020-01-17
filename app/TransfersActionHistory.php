<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransfersActionHistory extends Model
{
    protected $fillable = ['transfer_id', 'client_id', 'receiver_name', 'receiver_phone', 'sum', 'method', 'notation', 'status', 'user_id', 'issued_by'];

    public function getCreatedAtAttribute($value)
    {
        if ($value) {
            return \Illuminate\Support\Carbon::parse($value)->toAtomString();
        }
        return null;
    }

    public function getIssuedByAttribute($value)
    {
        if ($value) {
            return \Illuminate\Support\Carbon::parse($value)->toAtomString();
        }
        return null;
    }
}

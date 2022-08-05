<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Debt extends Model
{
    protected $fillable = ['code_client_id', 'type', 'sum', 'notation', 'user_id', 'transfer_id', 'paid', 'created_at', 'commission', 'get_pay_user_id', 'zero'];

    public function getCreatedAtAttribute($value)
    {
        if ($value) {
            return \Illuminate\Support\Carbon::parse($value)->toAtomString();
        }
        return null;
    }
}
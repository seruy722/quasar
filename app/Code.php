<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Code extends Model
{
    protected $fillable = ['code', 'user_id'];

    public function customers()
    {
        return $this->hasMany('App\Customer', 'code_id', 'id')->leftJoin('cities', 'cities.id', '=', 'customers.city_id')->select('customers.*', 'cities.name AS city_name');
    }

    public function user()
    {
        return $this->hasOne('App\User', 'id', 'user_id');
    }

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

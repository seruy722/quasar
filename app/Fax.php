<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Fax extends Model
{
    protected $fillable = ['name', 'departure_date', 'transport_id', 'transporter_id', 'delivered', 'user_id'];

    public function getDepartureDateAttribute($value)
    {
        return \Illuminate\Support\Carbon::parse($value)->format('Y/m/d');
    }

//    public function setDepartureDateAttribute($value)
//    {
//        $this->attributes['departure_date'] = strtotime($value);
//    }

    public function transport()
    {
        return $this->hasOne('App\Transport', 'id', 'transport_id');
    }

    public function transporter()
    {
        return $this->hasOne('App\Transporter', 'id', 'transporter_id');
    }

    public function user()
    {
        return $this->hasOne('App\User', 'id', 'user_id');
    }

    public function faxData()
    {
        return $this->hasMany('App\FaxData', 'fax_id', 'id');
    }
}

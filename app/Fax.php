<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Fax extends Model
{
    protected $fillable = ['name', 'departure_date', 'transport_id', 'transporter_id', 'status', 'user_id', 'arrival_date', 'paid', 'uploaded_to_cargo', 'notation'];

    public function getDepartureDateAttribute($value)
    {
        if ($value) {
            return \Illuminate\Support\Carbon::parse($value)->toAtomString();
        }
        return null;
    }

    public function getArrivalDateAttribute($value)
    {
        if ($value) {
            return \Illuminate\Support\Carbon::parse($value)->toAtomString();
        }
        return null;
    }

    public function getCreatedAtAttribute($value)
    {
        if ($value) {
            return \Illuminate\Support\Carbon::parse($value)->toAtomString();
        }
        return null;
    }

//    public function transport()
//    {
//        return $this->hasOne('App\Transport', 'id', 'transport_id');
//    }
//
//    public function transporter()
//    {
//        return $this->hasOne('App\Transporter', 'id', 'transporter_id');
//    }
//
//    public function user()
//    {
//        return $this->hasOne('App\User', 'id', 'user_id');
//    }
//
//    public function faxData()
//    {
//        return $this->hasMany('App\FaxData', 'fax_id', 'id');
//    }
}

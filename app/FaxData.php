<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FaxData extends Model
{
    protected $fillable = ['code', 'fax_id', 'code_id', 'place', 'kg', 'shop', 'things', 'city', 'notation', 'category_id', 'brand', 'for_kg', 'for_place'];

    public function fax()
    {
        return $this->hasOne('App\Fax', 'id', 'fax_id');
    }

    public function customer()
    {
        return $this->hasOne('App\Code', 'id', 'code_id');
    }

    public function category()
    {
        return $this->hasOne('App\Category', 'id', 'category_id');
    }
}

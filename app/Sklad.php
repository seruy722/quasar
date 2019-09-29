<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sklad extends Model
{
    protected $fillable = ['code', 'code_id', 'place', 'kg', 'shop', 'things', 'city', 'notation', 'category_id'];

    public function customer()
    {
        return $this->hasOne('App\Code', 'id', 'code_id');
    }

    public function category()
    {
        return $this->hasOne('App\Category', 'id', 'category_id');
    }
}

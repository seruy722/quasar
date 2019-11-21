<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    protected $table = 'test';
    protected $fillable = ['code', 'client', 'place', 'kg', 'shop', 'things', 'notation', 'category', 'brand', 'fax_name'];
}

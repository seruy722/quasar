<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CategoriesPrice extends Model
{
    protected $fillable = ['category_id', 'category_price'];
}

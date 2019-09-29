<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CodePrice extends Model
{
    protected $fillable = ['code_id', 'category_id', 'for_kg', 'for_place', 'user_id'];
}

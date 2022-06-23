<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = ['name', 'phone', 'city_id', 'sex', 'code_id', 'telegram_user_id'];
}

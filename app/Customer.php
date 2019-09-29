<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = ['name', 'email', 'phone', 'city', 'sex', 'notify', 'code_id', 'user_id'];
}

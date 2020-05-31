<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CodeHasDeliveryMethod extends Model
{
    protected $fillable = ['code_id', 'delivery_method_id'];
}

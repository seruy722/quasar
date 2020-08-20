<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentArrear extends Model
{
    protected $fillable = ['table_name', 'entries_id'];
}

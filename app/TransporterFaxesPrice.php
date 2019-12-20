<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransporterFaxesPrice extends Model
{
    protected $fillable = ['fax_id', 'category_id', 'category_price'];
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransporterPrice extends Model
{
    protected $fillable = ['for_kg', 'category_id', 'transporter_id'];
}

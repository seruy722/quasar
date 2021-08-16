<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CodesSettings extends Model
{
    use HasFactory;

    protected $fillable = ['code_client_id', 'transfer_commission'];
}

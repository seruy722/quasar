<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bot extends Model
{
    use HasFactory;

    protected $fillable = ['contact_id', 'first_name', 'full_name', 'last_name', 'bot_id', 'phone', 'bot_name', 'channel', 'subscribe', 'confirmation_code', 'code_id'];
}

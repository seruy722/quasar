<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsArchive extends Model
{
    use HasFactory;

    protected $fillable = ['fax_id', 'user_id', 'result', 'type', 'uid'];
}

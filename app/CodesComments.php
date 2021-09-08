<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CodesComments extends Model
{
    use HasFactory;

    protected $fillable = ['code_id', 'comment'];
}

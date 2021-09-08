<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CodesStatistics extends Model
{
    use HasFactory;

    protected $fillable = ['code_id', 'code_name', 'city_name', 'last_active_cargo', 'client_created_at', 'index', 'client_name'];

    public function comments()
    {
        return $this->hasMany('App\CodesComments', 'code_id', 'id')->orderBy('created_at', 'desc');
    }
}

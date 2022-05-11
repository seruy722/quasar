<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Statuses;
//use Illuminate\Http\Request;

class StatusesController extends Controller
{
    public function index()
    {
        return Statuses::select('id as value', 'name as label')->orderBy('name')->get();
    }
}

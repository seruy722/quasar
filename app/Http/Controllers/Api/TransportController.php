<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\TransportResource;
use App\Transport;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TransportController extends Controller
{
    public function index()
    {
        return response(['transports' => TransportResource::collection(Transport::all())]);
    }
}

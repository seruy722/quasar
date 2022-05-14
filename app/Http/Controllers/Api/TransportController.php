<?php

namespace App\Http\Controllers\Api;

use App\Transport;
use App\Http\Controllers\Controller;

class TransportController extends Controller
{
    public function getTransport()
    {
        return Transport::select('id as value', 'name as label')->orderBy('value')->get();
    }

    public function index()
    {
        return response(['transports' => $this->getTransport()]);
    }
}

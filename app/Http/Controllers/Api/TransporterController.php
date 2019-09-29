<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\TransportResource;
use App\Transporter;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TransporterController extends Controller
{
    public function index()
    {
        return response(['transporters' => TransportResource::collection(Transporter::all())]);
    }
}

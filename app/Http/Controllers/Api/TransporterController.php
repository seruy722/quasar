<?php

namespace App\Http\Controllers\Api;

use App\Transporter;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TransporterController extends Controller
{

    public function getTransporters()
    {
        return Transporter::select('id as value', 'name as label')->orderBy('value')->get();
    }

    public function index()
    {
        return response(['transporters' => $this->getTransporters()]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:transporters|max:255',
        ]);

        $transporter = Transporter::create([
            'name' => $request->name,
            'user_id' => auth()->user()->id,
        ]);

        return response(['transporter' => ['label' => $transporter->name, 'value' => $transporter->id]]);
    }
}

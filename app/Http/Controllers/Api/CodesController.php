<?php

namespace App\Http\Controllers\Api;

use App\Code;
use App\Customer;
use App\Imports\ImportData;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

class CodesController extends Controller
{
    public function uploadCodesData(Request $request)
    {
        if ($request->hasFile('upload')) {
            $ImportedFaxArray = Excel::toArray(new ImportData, $request->file('upload'));
            foreach ($ImportedFaxArray as $item) {
                foreach ($item as $elem) {
                    $trimElem = trim(implode($elem));
                    $isElem = Code::where('code', $trimElem)->first();
                    if (!$isElem) {
                        Code::create([
                            'code' => $trimElem,
                            'user_id' => rand(1, 4),
                        ]);
                    }

                }
            }
        }
//        $baza = DB::table('bazas')->whereNotNull('code')->get();
//        return response(['count' => $baza->count()]);
//        $unique = $baza->unique('phone');
//        $unique->each(function ($item) {
//            $code = Code::where('code', $item->code)->first();
//            Customer::create([
//                'name'=> $item->name,
//                'phone'=> $item->phone,
//                'notify'=> 1,
//                'code_id'=> $code ? $code->id : 0,
//                'user_id'=> 1,
//            ]);
//        });
    }

    public function checkCodeExist($code)
    {
        if (!$code) {
            throw ValidationException::withMessages([
                'code' => [trans('validation.required')],
            ]);
        } else {
            $code = Code::where('code', $code)->first();
            if ($code) {
                throw ValidationException::withMessages([
                    'code' => [trans('validation.codeExist')],
                ]);
            }
        }

        return response(['status' => true]);
    }

    public function storeCode(Request $request)
    {
        $this->validate($request, [
            'code' => 'required|unique:codes|max:255',
        ]);

        $code = Code::create([
            'code' => $request->code,
            'user_id' => $request->user()->id,
        ]);

        return response(['status' => true, 'code' => ['label' => $code->code, 'value' => $code->id]]);

    }
}

<?php

namespace App\Http\Controllers\Api;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use App\Traits\UserSetAccessData;

class AuthController extends Controller
{
    use UserSetAccessData;
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:55',
            'email' => 'email|required|unique:users',
            'password' => 'required|confirmed'
        ]);

        $validatedData['password'] = bcrypt($request->password);

        $user = User::create($validatedData);

        $accessToken = $user->createToken('authToken')->token;
        $accessToken->expires_at = now()->addDays(7);
        $accessToken->save();

        return response(['user' => $this->setAccessData(), 'access_token' => $accessToken->id]);

    }


    public function login(Request $request)
    {
        $loginData = $request->validate([
            'email' => 'email|required',
            'password' => 'required|max:255'
        ]);

        if (!auth()->attempt($loginData)) {
            throw ValidationException::withMessages([
                'email' => [trans('auth.combine')],
                'password' => [trans('auth.combine')],
            ]);
        }

        $accessToken = auth()->user()->createToken('authToken');
        $expData = $accessToken->token;
        $expData->expires_at = now()->addDays(7);
        $expData->save();

        return response(['user' => $this->setAccessData(), 'access_token' => $accessToken->accessToken]);

    }
}

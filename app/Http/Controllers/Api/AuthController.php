<?php

namespace App\Http\Controllers\Api;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;
use App\Traits\UserSetAccessData;
use GuzzleHttp\Client;

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

        return response(['user' => $this->setAccessData($user), 'access_token' => $accessToken->id]);

    }

    public function login(Request $request)
    {
        $loginData = $request->validate([
            'email' => 'required|max:255',
            'password' => 'required|max:255'
        ]);

        if (!auth()->attempt($loginData)) {
            throw ValidationException::withMessages([
                'email' => [trans('auth.combine')],
                'password' => [trans('auth.combine')],
            ]);
        }
        $user = auth()->user();

        $accessToken = auth()->user()->createToken('authToken');
        $expData = $accessToken->token;
        $expData->expires_at = now()->addDays(7);
        $expData->save();

        return response(['user' => $this->setAccessData($user), 'access_token' => $accessToken->accessToken]);
    }

    public function getCodeForRegister(Request $request)
    {
        $yesPhoneInDB = \App\Customer::where('phone', $request->phone)->first();
        if ($yesPhoneInDB && User::where('code_id', $yesPhoneInDB->code_id)->first()) {
            return response(['codeStatus' => 1, 'message' => 'Вы уже зарегестрированы. Выполните вход пожалуйста']);
        } else if ($yesPhoneInDB) {
            $code = $this->getRandCode();
            $data = $request->obj;
            $data['sms']['text'] = $data['sms']['text'] . ' ' . $code;
            $this->sendSms($data);
            Cache::put($yesPhoneInDB->phone, ['code' => $code, 'code_id' => $yesPhoneInDB->code_id], 300);
            return response(['codeStatus' => 3]);
        }
        return response(['codeStatus' => 2, 'message' => 'Номера телефона нет в базе']);
    }

    public function getRandCode(): int
    {
        return rand(111111, 999999);
    }

    public function getCodeForChangePassword(Request $request)
    {
        $yesPhoneInDB = \App\Customer::where('phone', $request->phone)->first();
        if ($yesPhoneInDB && User::where('code_id', $yesPhoneInDB->code_id)->first()) {
            $code = $this->getRandCode();
            $data = $request->obj;
            $data['sms']['text'] = $data['sms']['text'] . ' ' . $code;
            $this->sendSMS($data);
            Cache::put($yesPhoneInDB->phone, ['code' => $code, 'code_id' => $yesPhoneInDB->code_id], 300);
            return response(['codeStatus' => 3]);
        }
        return response(['codeStatus' => 2, 'message' => 'Номера телефона нет в базе']);
    }

    public function changePassword(Request $request)
    {
        if (Cache::has($request->phone) && Cache::get($request->phone)['code'] == $request->code) {
            $request->validate([
                'password' => 'required|confirmed'
            ]);
            $data = [
                'password' => bcrypt($request->password),
            ];
            $code = \App\Code::find(Cache::get($request->phone)['code_id']);
            if ($code) {
                User::where('phone', $request->phone)->update($data);
                Cache::forget($request->phone);
            }

            return response(['пароль изменен' => true]);
        }
        return response(['error' => 'Неверный код подтверждения или срок действия кода истек']);
    }

    public function registerClient(Request $request)
    {
        if (Cache::has($request->phone) && Cache::get($request->phone)['code'] == $request->code) {
            $request->validate([
                'password' => 'required|confirmed'
            ]);
            $data = [
                'password' => bcrypt($request->password),
            ];
            $code = \App\Code::find(Cache::get($request->phone)['code_id']);
            if ($code) {
                $data['name'] = $code->code;
                $data['email'] = $request->phone;
                $data['phone'] = $request->phone;
                $data['code_id'] = $code->id;
                $data['type'] = 'client';
            }
            $user = User::create($data);
            $user->givePermissionTo('exit app');
            $user->assignRole('client');
            Cache::forget($request->phone);

            return response(['register' => true]);
        }
        return response(['error' => 'Неверный код подтверждения или срок действия кода истек']);
    }

    public function sendSms($data)
    {
        $client = new Client();
        return $client->post("https://api.turbosms.ua/message/send.json", ['body' => json_encode($data), 'headers' => [
            'Content-Type' => 'application/json',
            "Authorization" => 'Bearer 55090e130c778e25675c1580655da1d0c8e89f43',
        ]]);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
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
            $code = rand(111111, 999999);
            $this->sendSMSForRegister($yesPhoneInDB->phone, $code);
            Cache::put($yesPhoneInDB->phone, ['code' => $code, 'code_id' => $yesPhoneInDB->code_id], 300);
            return response(['codeStatus' => 3]);
        }
        return response(['codeStatus' => 2, 'message' => 'Номера телефона нет в базе']);
    }

    public function getCodeForChangePassword(Request $request)
    {
        $yesPhoneInDB = \App\Customer::where('phone', $request->phone)->first();
        if ($yesPhoneInDB && User::where('code_id', $yesPhoneInDB->code_id)->first()) {
            $code = rand(111111, 999999);
            $this->sendSMSForRegister($yesPhoneInDB->phone, $code);
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

    public function sendSMSForRegister($phone, $code)
    {
        $text = iconv('windows-1251', 'utf-8', htmlspecialchars('Code cargo 007: ' . $code));
        $description = iconv('windows-1251', 'utf-8', htmlspecialchars('Моя первая рассылка'));
        $start_time = 'AUTO'; // отправить немедленно или ставим дату и время  в формате YYYY-MM-DD HH:MM:SS
        $end_time = 'AUTO'; // автоматически рассчитать системой или ставим дату и время  в формате YYYY-MM-DD HH:MM:SS
        $rate = 1; // скорость отправки сообщений (1 = 1 смс минута). Одиночные СМС сообщения отправляются всегда с максимальной скоростью.
        $lifetime = 4; // срок жизни сообщения 4 часа
        $recipient = $phone;
        $user = '380977376062'; // тут ваш логин в международном формате без знака +. Пример: 380501234567
        $password = 'seruy123'; // Ваш пароль

        $newsXML = new \SimpleXMLElement("<request></request>");
        $newsXML->addChild('operation', 'SENDSMS');
        $newsOp = $newsXML->addChild('message');
        $newsOp->addChild('body', $text);
        $newsOp->addChild('recipient', $recipient);
        $newsOp->addAttribute('start_time', $start_time);
        $newsOp->addAttribute('end_time', $end_time);
        $newsOp->addAttribute('lifetime', $lifetime);
        $newsOp->addAttribute('rate', $rate);
        $newsOp->addAttribute('desc', $description);

        /*        $myXML = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";*/
//        $myXML .= "<request>" . "\n";
//        $myXML .= "<operation>SENDSMS</operation>" . "\n";
//        $myXML .= '		<message start_time="' . $start_time . '" end_time="' . $end_time . '" lifetime="' . $lifetime . '" rate="' . $rate . '" desc="' . $description . '">' . "\n";
//        $myXML .= "		<body>" . $text . "</body>" . "\n";
//        $myXML .= "		<recipient>" . $recipient . "</recipient>" . "\n";
//        $myXML .= "</message>" . "\n";
//        $myXML .= "</request>";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_USERPWD, $user . ':' . $password);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, 'http://sms-fly.com/api/api.noai.php');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: text/xml", "Accept: text/xml"));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $newsXML->asXML());
        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }
}

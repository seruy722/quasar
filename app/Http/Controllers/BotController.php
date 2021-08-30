<?php

namespace App\Http\Controllers;

use App\Bot;
use App\Customer;
use App\Settings;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class BotController extends Controller
{
    protected $rules = [
        'contact_id' => 'required|string|max:100',
        'first_name' => 'nullable|string|max:100',
        'full_name' => 'nullable|string|max:100',
        'last_name' => 'nullable|string|max:100',
        'bot_id' => 'nullable|string|max:100',
        'phone' => 'nullable|string|max:100',
        'bot_name' => 'required|string|max:100',
        'channel' => 'required|string|max:100',
        'subscribe' => 'required|boolean',
    ];

    public function stripData($value)
    {
        $func = function ($value) {
            if (!$value) {
                return null;
            }
            return $value;
        };
        $arr = array_map("trim", $value);
        $arr = array_map("stripcslashes", $arr);
        $arr = array_map("strip_tags", $arr);
        $arr = array_map($func, $arr);
        return $arr;
    }

    // Добавляет клиента в базу когдаон подписывается на бота
    public function subscribeClient(Request $request)
    {
        $data = $request->all();
        $rul = [];
        foreach ($this->rules as $key => $value) {
            if (array_key_exists($key, $data)) {
                $rul[$key] = $value;
            }
        }

        $this->validate($request, $rul);
        $createData = $this->stripData($data);
        $createData['subscribe'] = !!$createData['subscribe'];

        Bot::updateOrCreate(['contact_id' => $data['contact_id']], $createData);

        return $createData;

    }

    // Отправляет смс
    public function sendSms($data)
    {
        $client = new Client();
        return $client->post("https://api.turbosms.ua/message/send.json", ['body' => json_encode($data), 'headers' => [
            'Content-Type' => 'application/json',
            "Authorization" => 'Bearer d4682d75313aaa3b83ac59cfdfdc4c6822610581',
        ]]);
    }

    public function getRandCode(): int
    {
        return rand(111111, 999999);
    }

    // Отправляет код подтверждения на телефон
    public function sendConfirmationCode(Request $request)
    {
        if ($request->phone) {
            $phone = preg_replace('/\D/', '', $request->phone);
            $customer = Customer::where('phone', $phone)->first();
            if ($customer) {
                $randNumber = $this->getRandCode();
                Bot::where('contact_id', $request->contact_id)->update(['confirmation_code' => $randNumber, 'code_id' => intval($customer->code_id), 'phone' => $phone]);
                $data = ['recipients' => [$phone], 'sms' => ['sender' => 'Cargo007', 'text' => 'Code for register: ' . $randNumber]];
                $this->sendSms($data);
                return $randNumber;
            }
        }

        return response(['status' => false]);
    }

    public function checkIsCustomerRegisterInProgram(Request $request)
    {
        $phone = preg_replace('/\D/', '', $request->phone);
        $customer = Customer::where('phone', $phone)->first();
        $client = new Client();
        $tokenData = Settings::where('bot_name', 'sendpulse')->first();
        $client->post("https://api.sendpulse.com/telegram/contacts/setVariable", ['body' => json_encode([
            'contact_id' => $request->contact_id,
            'variable_name' => 'checkIsCustomerRegisterInProgram',
            'variable_value' => $customer ? 1 : 0,
        ]), 'headers' => [
            'Content-Type' => 'application/json',
            "Authorization" => 'Bearer ' . $tokenData->token,
        ]]);
        if ($customer) {
            return response(['status' => 'Проверка прошла успешно!']);
        }
        return response(['status' => 'Вашего телефона нет в нашей базе. Обратитесь к менеджеру для его добавления.']);
    }

    public function test()
    {
        $client = new Client();
        $tokenData = Settings::where('bot_name', 'sendpulse')->first();
        $res = $client->get("https://api.sendpulse.com/telegram/contacts/get?id=61250da6c5d7a33b751d3d03", ['body' => json_encode([
            'id' => '61250da6c5d7a33b751d3d03',
        ]), 'headers' => [
            'Content-Type' => 'application/json',
            "Authorization" => 'Bearer ' . $tokenData->token,
        ]]);
        $body = $res->getBody();
        $data = json_decode($body->getContents());
        return response(['res' => $data]);
    }

    // Проверяет введенный код присланный от бота
    public function checkConfirmationCode(Request $request)
    {
        $contact = Bot::where('contact_id', $request->contact_id)->first();
        if ($contact && intval($contact->confirmation_code) === intval($request->userCode)) {
            return response(['status' => 'Подтверждение прошло успешно!']);
        }
        Bot::where('contact_id', $request->contact_id)->update(['confirmation_code' => 0, 'phone' => null, 'code_id' => 0]);

        $client = new Client();
        $tokenData = Settings::where('bot_name', 'sendpulse')->first();
        $variables = ['confirmation_code' => 0, 'phone' => '+380970000000'];
        foreach ($variables as $key => $value) {
            $client->post("https://api.sendpulse.com/telegram/contacts/setVariable", ['body' => json_encode([
                'contact_id' => $request->contact_id,
                'variable_name' => $key,
                'variable_value' => $value,
            ]), 'headers' => [
                'Content-Type' => 'application/json',
                "Authorization" => 'Bearer ' . $tokenData->token,
            ]]);
        }

        return response(['status' => 'Вы ввели неверный код подтверждения. Для повторной подписки введите «/stop» потом «/start»']);
    }
}

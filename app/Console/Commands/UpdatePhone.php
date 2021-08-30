<?php

namespace App\Console\Commands;

use App\Bot;
use App\Settings;
use Illuminate\Console\Command;
use GuzzleHttp\Client;

class UpdatePhone extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:phone';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'update phone from bot telegram and whatsapp';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $data = Bot::where('channel', 'telegram‌‌‌‌‌‌‌‌‌‌‌‌‌‌‌‌‌‌‌‌‌‌‌')->whereNull('phone')->get();
        if(!$data->isEmpty()){
            $tokenData = Settings::where('bot_name', 'sendpulse')->first();
            $client = new Client();
            foreach ($data as $item) {
                $res = $client->get("https://api.sendpulse.com/telegram/contacts/get", ['body' => json_encode([
                    'id' => $item['contact_id'],
                ]), 'headers' => [
                    'Content-Type' => 'application/json',
                    "Authorization" => 'Bearer ' . $tokenData->token,
                ]]);
                $body = $res->getBody();
                $data = json_decode($body->getContents());
                Bot::updateOrCreate(['bot_name' => 'sendpulse'], ['token' => $data->access_token, 'bot_name' => 'sendpulse']);
            }
        }

        return true;
    }
}

<?php

namespace App\Console\Commands;

use App\Settings;
use Illuminate\Console\Command;
use GuzzleHttp\Client;

class GetBotToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:bot:token';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'get token for bot telegram and whatsapp';

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
        $client = new Client();
        $res = $client->post("https://api.sendpulse.com/oauth/access_token", ['body' => json_encode([
            "grant_type" => "client_credentials",
            "client_id" => "bf80c3612bef57cca54aa8ea12875a20",
            "client_secret" => "27b42865061b66c7f59bd1a0ea419a71"
        ]), 'headers' => [
            'Content-Type' => 'application/json',
        ]]);
        $body = $res->getBody();
        $data = json_decode($body->getContents());
        Settings::updateOrCreate(['bot_name' => 'sendpulse'], ['token' => $data->access_token, 'bot_name' => 'sendpulse']);
        return true;
    }
}

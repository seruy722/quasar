<?php


namespace App\Traits;

trait PushNotifications
{
    protected $appId = '687eac00-a315-412d-b9a3-147dc96f7f54';

    public function createNotification($data)
    {
        $content = array(
            "en" => $data['text']
        );

        $headings = array(
            "en" => '007 ГЕНА КАРГО'
        );
        $fields = array(
            'app_id' => $this->appId,
            'include_player_ids' => $data['player_ids'],
            'data' => array(
                "user_id" => "bar"
            ),
            'contents' => $content,
            'headings' => $headings,
            'url' => $data['url']
        );

        $fields = json_encode($fields);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json; charset=utf-8',
            'Authorization: Basic NWY5MDRiY2YtYmUwYi00ZjRhLTgyZGYtY2I1NGMyMzUwMTIx'
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $response = curl_exec($ch);
        curl_close($ch);
    }
}

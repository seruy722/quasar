<?php

namespace App\Http\Controllers\Api;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NotificationController extends Controller
{
//    public function addDevice(Request $request)
//    {
//        $identifier = auth()->user()->identifier;
//        if (!$identifier) {
//            $identifier = Str::random(64);
//            User::where('id', auth()->user()->id)->update(['identifier' => $identifier]);
//        }
//        $fields = array(
//            'app_id' => $request->appId,
//            'identifier' => $identifier,
//            'language' => "ru",
//            'timezone' => $request->timezone,
//            'game_version' => "1.15",
//            'device_os' => $request->device_os,
//            'device_type' => "5",
//            'device_model' => $request->device_model,
//            'tags' => array("user_id" => auth()->user()->id)
//        );
//
//        $fields = json_encode($fields);
//
//        $ch = curl_init();
//        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/players");
//        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
//        curl_setopt($ch, CURLOPT_HEADER, FALSE);
//        curl_setopt($ch, CURLOPT_POST, TRUE);
//        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
//        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
//
//        $response = curl_exec($ch);
//        curl_close($ch);
//
//        $answer = json_decode($response);
//
//        if ($answer->success) {
//            User::where('id', auth()->user()->id)->update(['player_id' => $answer->id]);
//        }
//
//        return response()->json(json_decode($response));
//    }

//    public function createNotification(Request $request)
//    {
//        $content = array(
//            "en" => $request->text
//        );
//
//        $headings = array(
//            "en" => '007 ГЕНА КАРГО'
//        );
//        $fields = array(
//            'app_id' => $request->appId,
//            'include_player_ids' => array($request->id),
//            'data' => array(
//                "user_id" => "bar"
//            ),
//            'contents' => $content,
//            'headings' => $headings,
//        );
//
//        $fields = json_encode($fields);
//
//        $ch = curl_init();
//        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
//        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
//            'Content-Type: application/json; charset=utf-8',
//            'Authorization: Basic NWY5MDRiY2YtYmUwYi00ZjRhLTgyZGYtY2I1NGMyMzUwMTIx'
//        ));
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
//        curl_setopt($ch, CURLOPT_HEADER, FALSE);
//        curl_setopt($ch, CURLOPT_POST, TRUE);
//        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
//        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
//
//        $response = curl_exec($ch);
//        curl_close($ch);
//
//        return response()->json(null, 201);
//    }

    public function updatePlayerId(Request $request)
    {
        $user = User::where('id', auth()->user()->id)->first();
        if (!$user->player_id) {
            $user->player_id = json_encode([$request->player_id]);
            $user->save();
        } else {
            $playerIds = json_decode($user->player_id);
            array_push($playerIds, $request->player_id);
            $playerIds = array_unique($playerIds);
            $user->player_id = json_encode($playerIds);
            $user->save();
        }
        return response()->json(null, 201);
    }
}

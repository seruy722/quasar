<?php
use Illuminate\Support\Facades\Route;
//use Illuminate\Support\Facades\Notification;
//use App\Notifications\Telegram;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
//
//Notification::route('telegram', '774874575')
//    ->notify(new Telegram);

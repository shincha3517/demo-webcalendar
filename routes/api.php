<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('events', function () {
    date_default_timezone_set('Asia/Bangkok'); // set timezone
//    $startDate = \Carbon\Carbon::createFromFormat('d M Y H:i:s:u', '07 August 2017 10:45:53:000');
//    $endDate = \Carbon\Carbon::createFromFormat('d M Y H:i:s:u', '07 August 2017 11:45:53:000');
//    $events = [
//        [
//        "id" => 293,
//        "title"=> "Event 1",
//        "url"=> "http://example.com",
//        "class"=> "event-important",
//        "start"=> $startDate->getTimestamp()*1000,
//        "end"=> $endDate->getTimestamp()*1000 // Milliseconds
//        ]
//    ];
    $events = [];
    for ($i = 1; $i <= 29; $i++) {    //from day 01 to day 15
        $data = date('Y-m-d', strtotime("+" . $i . " days"));
        $events[] = array(
            'id' => $i,
            'title' => 'Event name ' . $i,
            'url' => url('sms/'.$i),
            'class' => 'event-important',
            'start' => strtotime($data) . '000'
        );
    }
    return json_encode(["success"=>1 ,"result"=>$events]);
});

Route::get('events/{id}', function ($id) {

    $events = [];
    if($id ==1) {
        $startDate = \Carbon\Carbon::createFromFormat('d M Y H:i:s:u', '08 August 2017 10:45:53:000');
        $endDate = \Carbon\Carbon::createFromFormat('d M Y H:i:s:u', '08 August 2017 11:45:53:000');

        for ($i = 1; $i <= 15; $i++) {    //from day 01 to day 15
            $data = date('Y-m-d', strtotime("+" . $i . " days"));
            $events[] = array(
                'id' => $i,
                'title' => 'Event name ' . $i,
                'url' => url('sms/'.$i),
                'class' => 'event-important',
                'start' => strtotime($data) . '000'
            );
        }
    }

    if($id ==2) {
        $startDate = \Carbon\Carbon::createFromFormat('d M Y H:i:s:u', '08 August 2017 10:45:53:000');
        $endDate = \Carbon\Carbon::createFromFormat('d M Y H:i:s:u', '08 August 2017 11:45:53:000');

        for ($i = 15; $i <= 20; $i++) {    //from day 01 to day 15
            $data = date('Y-m-d', strtotime("+" . $i . " days"));
            $events[] = array(
                'id' => $i,
                'title' => 'Event name ' . $i,
                'url' => url('sms/'.$i),
                'class' => 'event-important',
                'start' => strtotime($data) . '000'
            );
        }
    }
    if($id ==3) {
        $startDate = \Carbon\Carbon::createFromFormat('d M Y H:i:s:u', '08 August 2017 10:45:53:000');
        $endDate = \Carbon\Carbon::createFromFormat('d M Y H:i:s:u', '08 August 2017 11:45:53:000');

        for ($i = 1; $i <= 4; $i++) {    //from day 01 to day 15
            $data = date('Y-m-d', strtotime("+" . $i . " days"));
            $events[] = array(
                'id' => $i,
                'title' => 'Event name ' . $i,
                'url' => url('sms/'.$i),
                'class' => 'event-important',
                'start' => strtotime($data) . '000'
            );
        }
    }
    return json_encode(["success"=>1 ,"result"=>$events]);
});

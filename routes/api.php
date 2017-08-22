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

    //user 1
    $startDate = \Carbon\Carbon::createFromFormat('d M Y H:i:s:u', '11 August 2017 10:45:53:000','Asia/Singapore');
    $endDate = \Carbon\Carbon::createFromFormat('d M Y H:i:s:u', '11 August 2017 11:45:53:000','Asia/Singapore');

    $events[] = [

        'id' => 1,
        'title' => 'Loram isue',
        'url' => url('sms/1'),
        'class' => 'event-important',
        'start' => $startDate->timestamp.'000',
        'end'=>$endDate->timestamp.'000'
    ];
    $events[] = [
        'id' => 2,
        'title' => 'Donec pede justo',
        'url' => url('sms/2'),
        'class' => 'event-warning',
        'start' => ($startDate->timestamp+3600).'000',
        'end'=> ($endDate->timestamp+3600).'000'

    ];
    $events[] = [
        'id' => 3,
        'title' => 'In enim justo, rhoncus ut.',
        'url' => url('sms/3'),
        'class' => 'event-success',
        'start' => ($startDate->timestamp+7200).'000',
        'end'=> ($endDate->timestamp+7200).'000'

    ];
    $startDate = \Carbon\Carbon::createFromFormat('d M Y H:i:s:u', '14 August 2017 17:45:53:000','Asia/Singapore');
    $endDate = \Carbon\Carbon::createFromFormat('d M Y H:i:s:u', '14 August 2017 20:45:53:000','Asia/Singapore');
    $events[] = [
        'id' => 4,
        'title' => 'Aliquam lorem ante',
        'url' => url('sms/4'),
        'class' => 'event-warning',
        'start' => ($startDate->timestamp).'000',
        'end'=> ($endDate->timestamp).'000'

    ];
    $events[] = [
        'id' => 5,
        'title' => 'Curabitur ullamcorper ultricies nisi',
        'url' => url('sms/5'),
        'class' => 'event-success',
        'start' => ($startDate->timestamp+7200).'000',
        'end'=> ($endDate->timestamp+7200).'000'

    ];
    $startDate = \Carbon\Carbon::createFromFormat('d M Y H:i:s:u', '21 August 2017 11:00:00:000','Asia/Singapore');
    $endDate = \Carbon\Carbon::createFromFormat('d M Y H:i:s:u', '21 August 2017 12:00:00:000','Asia/Singapore');


    $events[] = [

        'id' => 16,
        'title' => 'Makeit event 1',
        'url' => url('sms/16'),
        'class' => 'event-important',
        'start' => $startDate->getTimestamp()*1000,
        'end'=>$endDate->getTimestamp()*1000
    ];
    $events[] = [

        'id' => 17,
        'title' => 'Makeit event 2',
        'url' => url('sms/17'),
        'class' => 'event-important',
        'start' => ($startDate->getTimestamp()+3600)*1000,
        'end'=>($endDate->getTimestamp()+3600)*1000
    ];

    //user 2
    $startDate = \Carbon\Carbon::createFromFormat('d M Y H:i:s:u', '12 August 2017 06:45:53:000','Asia/Singapore');
    $endDate = \Carbon\Carbon::createFromFormat('d M Y H:i:s:u', '12 August 2017 07:45:53:000','Asia/Singapore');

    $events[] = [

        'id' => 6,
        'title' => 'Loram isue',
        'url' => url('sms/6'),
        'class' => 'event-important',
        'start' => $startDate->getTimestamp()*1000,
        'end'=>$endDate->getTimestamp()*1000
    ];
    $events[] = [
        'id' => 7,
        'title' => 'Donec pede justo',
        'url' => url('sms/7'),
        'class' => 'event-success',
        'start' => $startDate->getTimestamp()*1000+3600,
        'end'=> $endDate->getTimestamp()*1000+3600

    ];

    $startDate = \Carbon\Carbon::createFromFormat('d M Y H:i:s:u', '23 August 2017 14:45:53:000','Asia/Singapore');
    $endDate = \Carbon\Carbon::createFromFormat('d M Y H:i:s:u', '23 August 2017 15:45:53:000','Asia/Singapore');

    $events[] = [

        'id' => 8,
        'title' => 'Maecenas tempus',
        'url' => url('sms/8'),
        'class' => 'event-important',
        'start' => $startDate->getTimestamp()*1000,
        'end'=>$endDate->getTimestamp()*1000
    ];
    $events[] = [
        'id' => 9,
        'title' => 'Nullam quis ante',
        'url' => url('sms/9'),
        'class' => 'event-warning',
        'start' => $startDate->getTimestamp()*1000+3600,
        'end'=> $endDate->getTimestamp()*1000+3600

    ];
    $startDate = \Carbon\Carbon::createFromFormat('d M Y H:i:s:u', '25 August 2017 14:45:53:000','Asia/Singapore');
    $endDate = \Carbon\Carbon::createFromFormat('d M Y H:i:s:u', '25 August 2017 15:45:53:000','Asia/Singapore');

    $events[] = [

        'id' => 10,
        'title' => 'Maecenas tempus',
        'url' => url('sms/10'),
        'class' => 'event-important',
        'start' => $startDate->getTimestamp()*1000,
        'end'=>$endDate->getTimestamp()*1000
    ];


    //user 3
    $startDate = \Carbon\Carbon::createFromFormat('d M Y H:i:s:u', '16 August 2017 06:45:53:000','Asia/Singapore');
    $endDate = \Carbon\Carbon::createFromFormat('d M Y H:i:s:u', '16 August 2017 07:45:53:000','Asia/Singapore');

    $events[] = [

        'id' => 11,
        'title' => 'Loram isue',
        'url' => url('sms/11'),
        'class' => 'event-important',
        'start' => $startDate->getTimestamp()*1000,
        'end'=>$endDate->getTimestamp()*1000
    ];
    $events[] = [
        'id' => 12,
        'title' => 'Donec pede justo',
        'url' => url('sms/12'),
        'class' => 'event-success',
        'start' => $startDate->getTimestamp()*1000+3600,
        'end'=> $endDate->getTimestamp()*1000+3600

    ];

    $startDate = \Carbon\Carbon::createFromFormat('d M Y H:i:s:u', '19 August 2017 20:45:53:000','Asia/Singapore');
    $endDate = \Carbon\Carbon::createFromFormat('d M Y H:i:s:u', '19 August 2017 21:45:53:000','Asia/Singapore');

    $events[] = [

        'id' => 13,
        'title' => 'Maecenas tempus',
        'url' => url('sms/13'),
        'class' => 'event-important',
        'start' => $startDate->getTimestamp()*1000,
        'end'=>$endDate->getTimestamp()*1000
    ];
    $events[] = [
        'id' => 14,
        'title' => 'Nullam quis ante',
        'url' => url('sms/14'),
        'class' => 'event-warning',
        'start' => $startDate->getTimestamp()*1000+3600,
        'end'=> $endDate->getTimestamp()*1000+3600

    ];
    $startDate = \Carbon\Carbon::createFromFormat('d M Y H:i:s:u', '21 August 2017 14:45:53:000','Asia/Singapore');
    $endDate = \Carbon\Carbon::createFromFormat('d M Y H:i:s:u', '21 August 2017 15:45:53:000','Asia/Singapore');

    $events[] = [

        'id' => 15,
        'title' => 'Maecenas tempus',
        'url' => url('sms/15'),
        'class' => 'event-important',
        'start' => $startDate->getTimestamp()*1000,
        'end'=>$endDate->getTimestamp()*1000
    ];

    return json_encode(["success"=>1 ,"result"=>$events]);
});

Route::get('events/{id}', function ($id) {

    $events = [];
    if($id ==1) {
        $startDate = \Carbon\Carbon::createFromFormat('d M Y H:i:s:u', '11 August 2017 10:45:53:000','Asia/Singapore');
        $endDate = \Carbon\Carbon::createFromFormat('d M Y H:i:s:u', '11 August 2017 11:45:53:000','Asia/Singapore');

        $events[] = [

                'id' => 1,
                'title' => 'Loram isue',
                'url' => url('sms/1'),
                'class' => 'event-important',
                'start' => $startDate->timestamp.'000',
                'end'=>$endDate->timestamp.'000'
            ];
        $events[] = [
                'id' => 2,
                'title' => 'Donec pede justo',
                'url' => url('sms/2'),
                'class' => 'event-warning',
                'start' => ($startDate->timestamp+3600).'000',
                'end'=> ($endDate->timestamp+3600).'000'

        ];
        $events[] = [
            'id' => 3,
            'title' => 'In enim justo, rhoncus ut.',
            'url' => url('sms/3'),
            'class' => 'event-success',
            'start' => ($startDate->timestamp+7200).'000',
            'end'=> ($endDate->timestamp+7200).'000'

        ];
        $startDate = \Carbon\Carbon::createFromFormat('d M Y H:i:s:u', '14 August 2017 17:45:53:000','Asia/Singapore');
        $endDate = \Carbon\Carbon::createFromFormat('d M Y H:i:s:u', '14 August 2017 20:45:53:000','Asia/Singapore');
        $events[] = [
            'id' => 4,
            'title' => 'Aliquam lorem ante',
            'url' => url('sms/4'),
            'class' => 'event-warning',
            'start' => ($startDate->timestamp).'000',
            'end'=> ($endDate->timestamp).'000'

        ];
        $events[] = [
            'id' => 5,
            'title' => 'Curabitur ullamcorper ultricies nisi',
            'url' => url('sms/5'),
            'class' => 'event-success',
            'start' => ($startDate->timestamp+7200).'000',
            'end'=> ($endDate->timestamp+7200).'000'

        ];
        $startDate = \Carbon\Carbon::createFromFormat('d M Y H:i:s:u', '21 August 2017 11:00:00:000','Asia/Singapore');
        $endDate = \Carbon\Carbon::createFromFormat('d M Y H:i:s:u', '21 August 2017 12:00:00:000','Asia/Singapore');

        $events[] = [

            'id' => 16,
            'title' => 'Makeit event 1',
            'url' => url('sms/16'),
            'class' => 'event-important',
            'start' => $startDate->getTimestamp()*1000,
            'end'=>$endDate->getTimestamp()*1000
        ];
        $events[] = [

            'id' => 17,
            'title' => 'Makeit event 2',
            'url' => url('sms/17'),
            'class' => 'event-important',
            'start' => ($startDate->getTimestamp()+3600)*1000,
            'end'=>($endDate->getTimestamp()+3600)*1000
        ];
    }

    if($id ==2) {
        $startDate = \Carbon\Carbon::createFromFormat('d M Y H:i:s:u', '12 August 2017 06:45:53:000','Asia/Singapore');
        $endDate = \Carbon\Carbon::createFromFormat('d M Y H:i:s:u', '12 August 2017 07:45:53:000','Asia/Singapore');

        $events[] = [

            'id' => 6,
            'title' => 'Loram isue',
            'url' => url('sms/6'),
            'class' => 'event-important',
            'start' => $startDate->getTimestamp()*1000,
            'end'=>$endDate->getTimestamp()*1000
        ];
        $events[] = [
            'id' => 7,
            'title' => 'Donec pede justo',
            'url' => url('sms/7'),
            'class' => 'event-success',
            'start' => $startDate->getTimestamp()*1000+3600,
            'end'=> $endDate->getTimestamp()*1000+3600

        ];

        $startDate = \Carbon\Carbon::createFromFormat('d M Y H:i:s:u', '23 August 2017 14:45:53:000','Asia/Singapore');
        $endDate = \Carbon\Carbon::createFromFormat('d M Y H:i:s:u', '23 August 2017 15:45:53:000','Asia/Singapore');

        $events[] = [

            'id' => 8,
            'title' => 'Maecenas tempus',
            'url' => url('sms/8'),
            'class' => 'event-important',
            'start' => $startDate->getTimestamp()*1000,
            'end'=>$endDate->getTimestamp()*1000
        ];
        $events[] = [
            'id' => 9,
            'title' => 'Nullam quis ante',
            'url' => url('sms/9'),
            'class' => 'event-warning',
            'start' => $startDate->getTimestamp()*1000+3600,
            'end'=> $endDate->getTimestamp()*1000+3600

        ];
        $startDate = \Carbon\Carbon::createFromFormat('d M Y H:i:s:u', '25 August 2017 14:45:53:000','Asia/Singapore');
        $endDate = \Carbon\Carbon::createFromFormat('d M Y H:i:s:u', '25 August 2017 15:45:53:000','Asia/Singapore');

        $events[] = [

            'id' => 10,
            'title' => 'Maecenas tempus',
            'url' => url('sms/10'),
            'class' => 'event-important',
            'start' => $startDate->getTimestamp()*1000,
            'end'=>$endDate->getTimestamp()*1000
        ];
        $startDate = \Carbon\Carbon::createFromFormat('d M Y H:i:s:u', '21 August 2017 11:00:00:000','Asia/Singapore');
        $endDate = \Carbon\Carbon::createFromFormat('d M Y H:i:s:u', '21 August 2017 12:00:00:000','Asia/Singapore');

        $events[] = [

            'id' => 16,
            'title' => 'Makeit event 1',
            'url' => url('sms/16'),
            'class' => 'event-important',
            'start' => $startDate->getTimestamp()*1000,
            'end'=>$endDate->getTimestamp()*1000
        ];
        $events[] = [

            'id' => 17,
            'title' => 'Makeit event 2',
            'url' => url('sms/17'),
            'class' => 'event-important',
            'start' => ($startDate->getTimestamp()+3600)*1000,
            'end'=>($endDate->getTimestamp()+3600)*1000
        ];
    }
    if($id ==3) {
        $startDate = \Carbon\Carbon::createFromFormat('d M Y H:i:s:u', '16 August 2017 06:45:53:000','Asia/Singapore');
        $endDate = \Carbon\Carbon::createFromFormat('d M Y H:i:s:u', '16 August 2017 07:45:53:000','Asia/Singapore');

        $events[] = [

            'id' => 11,
            'title' => 'Loram isue',
            'url' => url('sms/11'),
            'class' => 'event-important',
            'start' => $startDate->getTimestamp()*1000,
            'end'=>$endDate->getTimestamp()*1000
        ];
        $events[] = [
            'id' => 12,
            'title' => 'Donec pede justo',
            'url' => url('sms/12'),
            'class' => 'event-success',
            'start' => $startDate->getTimestamp()*1000+3600,
            'end'=> $endDate->getTimestamp()*1000+3600

        ];

        $startDate = \Carbon\Carbon::createFromFormat('d M Y H:i:s:u', '19 August 2017 20:45:53:000','Asia/Singapore');
        $endDate = \Carbon\Carbon::createFromFormat('d M Y H:i:s:u', '19 August 2017 21:45:53:000','Asia/Singapore');

        $events[] = [

            'id' => 13,
            'title' => 'Maecenas tempus',
            'url' => url('sms/13'),
            'class' => 'event-important',
            'start' => $startDate->getTimestamp()*1000,
            'end'=>$endDate->getTimestamp()*1000
        ];
        $events[] = [
            'id' => 14,
            'title' => 'Nullam quis ante',
            'url' => url('sms/14'),
            'class' => 'event-warning',
            'start' => $startDate->getTimestamp()*1000+3600,
            'end'=> $endDate->getTimestamp()*1000+3600

        ];
        $startDate = \Carbon\Carbon::createFromFormat('d M Y H:i:s:u', '21 August 2017 11:00:00:000','Asia/Singapore');
        $endDate = \Carbon\Carbon::createFromFormat('d M Y H:i:s:u', '21 August 2017 12:00:00:000','Asia/Singapore');

        $events[] = [

            'id' => 16,
            'title' => 'Makeit event 1',
            'url' => url('sms/16'),
            'class' => 'event-important',
            'start' => $startDate->getTimestamp()*1000,
            'end'=>$endDate->getTimestamp()*1000
        ];
        $events[] = [

            'id' => 17,
            'title' => 'Makeit event 2',
            'url' => url('sms/17'),
            'class' => 'event-important',
            'start' => ($startDate->getTimestamp()+3600)*1000,
            'end'=> ($endDate->getTimestamp()+3600)*1000
        ];
    }
    return json_encode(["success"=>1 ,"result"=>$events]);
});

<?php

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
    return view('index');
});

Route::get('sms/{id}', function ($id) {
    return view('sms',compact('id'));
});

Route::post('send-sms', function (\Illuminate\Http\Request $request) {
    $userId = $request->get('userId');
    if($userId){
        if($userId == 1){
            $phoneNumber = '+6594244449';
        }elseif($userId == 2){
            $phoneNumber = '+6594366266';
        }elseif($userId == 3){
            $phoneNumber = '+6594244449';
        }

        if(empty($phoneNumber)){
            $phoneNumber = '+6594244449';
        }
        $from = '+84986981718';

        Nexmo::message()->send([
            'to' => $phoneNumber,
            'from' => $from,
            'text' => 'Admin just assign you to new event'
        ]);

        return redirect()->to('send-successful');
    }
    else{
        return redirect()->to('send-fail');
    }
    return view('sms',compact('id'));
});

Route::get('/send-fail', function () {
    echo 'Can not send SMS';
});

Route::get('/send-successful', function () {
    echo 'System sent SMS to user, please check response.';
});
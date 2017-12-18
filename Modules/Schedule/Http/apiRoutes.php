<?php

use Illuminate\Routing\Router;
use Illuminate\Http\Request;

/** @var Router $router */
/** @var Request $request */

Route::group(['prefix' => 'v1'], function(Router $router) {

    $router->post('sms/receive-reply-sms', [
        'as' => 'api.sms.receive',
        'uses' => 'V1\SMSController@receiveSMSReply',
    ]);
});
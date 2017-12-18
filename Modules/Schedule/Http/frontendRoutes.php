<?php

use Illuminate\Routing\Router;
/** @var Router $router */

$router->group(['prefix' =>'/sms'], function (Router $router) {
    //callback reply sms
    $router->get('confirm', [
        'as' => 'sms.reply',
        'uses' => 'PublicController@confirm',
    ]);

    $router->get('receive-reply-sms', [
        'as' => 'sms.receive-reply',
        'uses' => 'PublicController@receiveSMSReply',
    ]);
    $router->post('receive-reply-sms', [
        'as' => 'sms.receive-reply',
        'uses' => 'PublicController@receiveSMSReply',
    ]);
});
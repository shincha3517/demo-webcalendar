<?php

use Illuminate\Routing\Router;
/** @var Router $router */

$router->group(['prefix' =>'/schedule'], function (Router $router) {
// append
    $router->get('upload-excel', [
        'as' => 'admin.schedule.upload.form',
        'uses' => 'ScheduleController@getUpload',
        'middleware' => 'can:schedule.schedules.upload',
    ]);
    $router->post('do-upload', [
        'as' => 'admin.schedule.upload.store',
        'uses' => 'ScheduleController@doUpload',
        'middleware' => 'can:schedule.schedules.upload',
    ]);
    $router->get('/', [
        'as' => 'admin.schedule.index',
        'uses' => 'ScheduleController@index',
        'middleware' => 'can:schedule.schedules.index',
    ]);
    $router->post('sync-data', [
        'as' => 'admin.schedule.sync',
        'uses' => 'ScheduleController@getSyncData',
        'middleware' => 'can:schedule.schedules.upload',
    ]);


    //api
    $router->get('/getUserByDate', [
        'as' => 'admin.schedule.getUserByDate',
        'uses' => 'ScheduleController@getUserByDate',
        'middleware' => 'can:schedule.schedules.index',
    ]);

    $router->get('/getUserTimeline', [
        'as' => 'admin.schedule.getUserTimeline',
        'uses' => 'ScheduleController@getUserTimeline',
        'middleware' => 'can:schedule.schedules.index',
    ]);

    $router->get('/getAvailableUser', [
        'as' => 'admin.schedule.getAvailableUser',
        'uses' => 'ScheduleController@getAvailableUser',
        'middleware' => 'can:schedule.schedules.index',
    ]);

    $router->post('send-sms', [
        'as' => 'admin.schedule.sendSMS',
        'uses' => 'ScheduleController@sendNotification',
        'middleware' => 'can:schedule.schedules.upload',
    ]);

    $router->get('/getUserByEvent', [
        'as' => 'admin.schedule.getUserByEvent',
        'uses' => 'ScheduleController@getUserByEvent',
        'middleware' => 'can:schedule.schedules.index',
    ]);
});

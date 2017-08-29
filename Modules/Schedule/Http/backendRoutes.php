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
});

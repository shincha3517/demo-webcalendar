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
    $router->get('worker', [
        'as' => 'admin.schedule.worker',
        'uses' => 'ScheduleController@actionWorker',
        'middleware' => 'can:schedule.schedules.worker',
    ]);


    //api
    $router->get('/getUserByDate', [
        'as' => 'admin.schedule.getUserByDate',
        'uses' => 'ScheduleController@getUserByDate',
//        'middleware' => 'can:schedule.schedules.index',
    ]);

    $router->get('/getUserTimeline', [
        'as' => 'admin.schedule.getUserTimeline',
        'uses' => 'ScheduleController@getUserSchedules',
//        'middleware' => 'can:schedule.schedules.index',
    ]);

    $router->get('/getAvailableUser', [
        'as' => 'admin.schedule.getAvailableUser',
        'uses' => 'ScheduleController@getAvailableUser',
        'middleware' => 'can:schedule.schedules.index',
    ]);
    $router->get('/getAvailableUserByEvents', [
        'as' => 'admin.schedule.getAvailableUserByEvents',
        'uses' => 'ScheduleController@getFreeUsersWithSchedule',
//        'middleware' => 'can:schedule.schedules.index',
    ]);

    $router->post('send-sms', [
        'as' => 'admin.schedule.sendSMS',
        'uses' => 'ScheduleController@sendNotification',
//        'middleware' => 'can:schedule.schedules.upload',
    ]);
    $router->post('send-absent-request', [
        'as' => 'admin.schedule.sendAbsentRequest',
        'uses' => 'ScheduleController@sendAbsentRequest',
//        'middleware' => 'can:schedule.schedules.upload',
    ]);

    $router->post('cancel', [
        'as' => 'admin.schedule.cancel',
        'uses' => 'ScheduleController@cancelReplaceTeacher',
//        'middleware' => 'can:schedule.schedules.upload',
    ]);

    $router->get('/getUserByEvent', [
        'as' => 'admin.schedule.getUserByEvent',
        'uses' => 'ScheduleController@getUserByEvent',
        'middleware' => 'can:schedule.schedules.index',
    ]);

    $router->get('upload-excel', [
        'as' => 'admin.schedule.upload.form',
        'uses' => 'ScheduleController@getUpload',
        'middleware' => 'can:schedule.schedules.upload',
    ]);

    $router->get('assign-form-modal', [
        'as' => 'admin.schedule.assign.form.modal',
        'uses' => 'ScheduleController@getAssignFormModal',
        'middleware' => 'can:schedule.schedules.index',
    ]);

    //REPORT
    $router->get('report', [
        'as' => 'admin.schedule.report',
        'uses' => 'ReportController@index',
        'middleware' => 'can:schedule.report.index',
    ]);

    $router->get('meeting', [
        'as' => 'admin.schedule.meeting.index',
        'uses' => 'ReportController@index',
        'middleware' => 'can:schedule.meeting.index',
    ]);

    $router->get('test-sms/{phone_number}/{body}', [
        'as' => 'admin.schedule.sms.test',
        'uses' => 'ScheduleController@testSMS',
        'middleware' => 'can:schedule.schedules.index',
    ]);


    $router->get('teacher', [
        'as' => 'admin.schedule.teacher.index',
        'uses' => 'TeacherController@index',
        'middleware' => 'can:schedule.teacher.index',
    ]);
    $router->get('teacher/{id}/edit', [
        'as' => 'admin.schedule.teacher.edit',
        'uses' => 'TeacherController@edit',
        'middleware' => 'can:schedule.teacher.edit',
    ]);
    $router->get('teacher/create', [
        'as' => 'admin.schedule.teacher.create',
        'uses' => 'TeacherController@create',
        'middleware' => 'can:schedule.teacher.create',
    ]);
    $router->post('teacher/create', [
        'as' => 'admin.schedule.teacher.store',
        'uses' => 'TeacherController@store',
        'middleware' => 'can:schedule.teacher.create',
    ]);
    $router->put('teacher/{id}/edit', [
        'as' => 'admin.schedule.teacher.update',
        'uses' => 'TeacherController@update',
        'middleware' => 'can:schedule.teacher.edit',
    ]);

    $router->get('test', function(){
        $notifyDate = \Carbon\Carbon::parse('2018-01-19 15:16:00');
        echo $notifyDate->diffInMinutes(\Carbon\Carbon::now()) .'<br/>';
        echo \Carbon\Carbon::now()->toDateTimeString();
    });
});
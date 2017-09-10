<?php

namespace Modules\Schedule\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Schedule\Events\Handlers\InsertTeacherSchedule;
use Modules\Schedule\Events\ImportExcelSchedule;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        ImportExcelSchedule::class => [
            InsertTeacherSchedule::class
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
<?php

namespace Modules\Schedule\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Core\Traits\CanPublishConfiguration;
use Modules\Core\Events\BuildingSidebar;
use Modules\Schedule\Events\Handlers\RegisterScheduleSidebar;

class ScheduleServiceProvider extends ServiceProvider
{
    use CanPublishConfiguration;
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerBindings();
        $this->app['events']->listen(BuildingSidebar::class, RegisterScheduleSidebar::class);
    }

    public function boot()
    {
        $this->publishConfig('schedule', 'permissions');
        $this->publishConfig('schedule', 'settings');

        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array();
    }

    private function registerBindings()
    {
// add bindings
        $this->app->bind(
            'Modules\Schedule\Repositories\TeacherRepository',
            function () {
                $repository = new \Modules\Schedule\Repositories\Eloquent\EloquentTeacherRepository(new \Modules\Schedule\Entities\Teacher());

                if (! config('app.cache')) {
                    return $repository;
                }

                return new \Modules\Schedule\Repositories\Cache\CacheTeacherDecorator($repository);
            }
        );
        $this->app->bind(
            'Modules\Schedule\Repositories\ScheduleRepository',
            function () {
                $repository = new \Modules\Schedule\Repositories\Eloquent\EloquentScheduleRepository(new \Modules\Schedule\Entities\Schedule());

                if (! config('app.cache')) {
                    return $repository;
                }

                return new \Modules\Schedule\Repositories\Cache\CacheScheduleDecorator($repository);
            }
        );
    }
}

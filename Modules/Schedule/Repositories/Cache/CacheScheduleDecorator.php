<?php

namespace Modules\Schedule\Repositories\Cache;

use Modules\Core\Repositories\Cache\BaseCacheDecorator;
use Modules\Schedule\Repositories\ScheduleRepository;
use Modules\Schedule\Repositories\TeacherRepository;

class CacheScheduleDecorator extends BaseCacheDecorator implements ScheduleRepository
{
    public function __construct(ScheduleRepository $notify)
    {
        parent::__construct();
        $this->entityName = 'schedule.schedule';
        $this->repository = $notify;
    }
}

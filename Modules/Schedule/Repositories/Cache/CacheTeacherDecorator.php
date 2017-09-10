<?php

namespace Modules\Schedule\Repositories\Cache;

use Modules\Core\Repositories\Cache\BaseCacheDecorator;
use Modules\Schedule\Repositories\TeacherRepository;

class CacheTeacherDecorator extends BaseCacheDecorator implements TeacherRepository
{
    public function __construct(TeacherRepository $notify)
    {
        parent::__construct();
        $this->entityName = 'schedule.teacher';
        $this->repository = $notify;
    }
}

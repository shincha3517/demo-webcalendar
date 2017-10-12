<?php

namespace Modules\Schedule\Repositories\Eloquent;

use Modules\Core\Repositories\Eloquent\EloquentBaseRepository;
use Modules\Schedule\Repositories\EventScheduleRepository;
use Modules\Schedule\Repositories\ScheduleRepository;
use Modules\Schedule\Repositories\TeacherRepository;

class EloquentEventScheduleRepository extends EloquentBaseRepository implements EventScheduleRepository
{
}

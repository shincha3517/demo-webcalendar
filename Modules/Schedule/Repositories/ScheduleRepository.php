<?php

namespace Modules\Schedule\Repositories;

use Modules\Core\Repositories\BaseRepository;

interface ScheduleRepository extends BaseRepository
{
    public function getOldTimeSlot($requestDate);
}

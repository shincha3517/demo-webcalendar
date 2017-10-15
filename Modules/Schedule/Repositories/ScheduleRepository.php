<?php

namespace Modules\Schedule\Repositories;

use Modules\Core\Repositories\BaseRepository;

interface ScheduleRepository extends BaseRepository
{
    public function getOldTimeSlot($requestDate);

    public function getUsersByDate($selectedDate);

    public function getUserSchedules($userId,$selectedDate);

    public function getFreeUserWithSchedules($date, $eventIds, $type);

    public function replaceTeacher($schedules,$replaceTeacherId,$replaceDate);

    public function getSchedulesInArray($ids = array());
}

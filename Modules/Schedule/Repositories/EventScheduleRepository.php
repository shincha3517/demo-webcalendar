<?php

namespace Modules\Schedule\Repositories;

use Modules\Core\Repositories\BaseRepository;

interface EventScheduleRepository extends BaseRepository
{
    public function getOldTimeSlot($requestDate);

    public function getUsersByDate($selectedDate);

    public function getUserSchedules($userId,$selectedDate);

    public function getFreeUserWithSchedules($date, $eventIds, $type);

    public function replaceTeacher($schedules,$replaceTeacherId,$replaceDate,$reason,$additionalRemark);

    public function getSchedulesInArray($ids = array());

    public function userCancelAssignSchedule($scheduleId,$date);
}

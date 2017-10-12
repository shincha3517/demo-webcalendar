<?php

namespace Modules\Schedule\Repositories\Eloquent;

use Carbon\Carbon;
use Modules\Core\Repositories\Eloquent\EloquentBaseRepository;
use Modules\Schedule\Entities\ScheduleDate;
use Modules\Schedule\Repositories\ScheduleRepository;
use Modules\Schedule\Repositories\TeacherRepository;

class EloquentScheduleRepository extends EloquentBaseRepository implements ScheduleRepository
{

    public function getOldTimeSlot($requestDate){
        $scheduleDate = ScheduleDate::first();

        $syStartTime = Carbon::parse($scheduleDate->start_date)->toTimeString();
        $interval = $scheduleDate->interval;
        $paramDate = $requestDate;

        $result = [];

        for($i=1; $i<=$scheduleDate->old_total_timeslots; $i++){
            $startDate = Carbon::createFromFormat('m/d/Y',$paramDate)->setTimeFromTimeString($syStartTime);
            if($i>1){
                $pushMinute = $i*$interval - $interval;
                $startDate = $startDate->addMinutes($pushMinute);
            }
            $startTime = substr($startDate->toTimeString(),0,-3);
            $endTime   = substr($startDate->addMinutes($interval)->toTimeString('h:m'),0,-3);

            $timeSlot = [
                'slot' => "$i",
                'start'=>$startTime,
                'end'=>$endTime
            ];
            array_push($result,$timeSlot);
        }
        return $result;
    }
}

<?php

namespace Modules\Schedule\Repositories\Eloquent;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Core\Repositories\Eloquent\EloquentBaseRepository;
use Modules\Schedule\Entities\Activity;
use Modules\Schedule\Entities\Schedule;
use Modules\Schedule\Entities\ScheduleDate;
use Modules\Schedule\Entities\Teacher;
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

    public function getUsersByDate($selectedDate){
        $date = Carbon::parse($selectedDate);
//        DB::enableQueryLog();
        $query = Schedule::with('teacher');
        if($date->isMonday()){
            $query->where('day_name','Monday');
        }elseif($date->isTuesday()){
            $query->where('day_name','Tuesday');
        }elseif($date->isWednesday()){
            $query->where('day_name','Wednesday');
        }elseif($date->isThursday()){
            $query->where('day_name','Thursday');
        }elseif($date->isFriday()){
            $query->where('day_name','Friday');
        }
        else{
            $query->whereNull('day_name');
        }
        $query->orderBy('teacher_id','ASC')->groupBy('teacher_id');
        $rows = $query->get();
//        dd(DB::getQueryLog());

        $result = [];
        if(count($rows) > 0){
            foreach($rows as $row){
                $teacher = [
                    'id'=>$row->teacher ? $row->teacher->id:0,
                    'text'=>$row->teacher ? $row->teacher->name: '',
                ];
                array_push($result,$teacher);
            }
        }
        $assignedTeacher = Activity::where('selected_date',$date->toDateString())->get();
        if(count($assignedTeacher) > 0){
            foreach($assignedTeacher as $row){
                $teacher = [
                    'id'=>$row->replaced_teacher_id ? $row->replaced_teacher_id:0,
                    'text'=>$row->replaceTeacher ? $row->replaceTeacher->name: '',
                ];
                array_push($result,$teacher);
            }
        }
        //sort list result
        ksort($result);

        return $result;
    }

    public function getUserSchedules($userId,$date){
        $teacherId = $userId;
        $teacher = Teacher::find($teacherId);

        $dayName = Carbon::parse($date);

        $query = Schedule::where('teacher_id',$teacherId);

        $dayNameData = '';
        if($dayName->isMonday()){
            $query->where('day_name','Monday');
            $dayNameData = 'Monday';
        }elseif($dayName->isTuesday()){
            $query->where('day_name','Tuesday');
            $dayNameData = 'Tuesday';
        }elseif($dayName->isWednesday()){
            $query->where('day_name','Wednesday');
            $dayNameData = 'Wednesday';
        }elseif($dayName->isThursday()){
            $query->where('day_name','Thursday');
            $dayNameData = 'Thursday';
        }elseif($dayName->isFriday()){
            $query->where('day_name','Friday');
            $dayNameData = 'Friday';
        }else{
            $query->whereNull('day_name');
            $dayNameData = '';
        }

        $query->groupBy('date_id');
        $rows= $query->get();


        $result = [];
        $group[] = [
            'id'=>$teacherId,
            'content'=> $teacher->name.'&nbsp;&nbsp;&nbsp;',
            'value'=>$teacherId
        ];

        //GET OLD TIME SLOT
        $timeData = $this->getOldTimeSlot($date);
        $result['data']['time_slot'] = $timeData;


        $result['data']['time_data'][0] = [];
        $result['data']['time_data'][0]['required']['teacher'] = $teacher->name;
        $classes = $result['data']['time_data'][0]['required']['classes'] = [];

//        $result['data']['time_data'][0]['paired'] =[];
        $pairs = [];
        $beAssigned = [];

        $assignedSchedules = Activity::where('teacher_id',$teacher->id)
            ->where('selected_date',$dayName->toDateString())
            ->get();
        $collectionSchedule = collect($assignedSchedules)->map(function($schedule){
            return $schedule->schedule_id;
        })->toArray();

        $beAssignedSchedules = Activity::where('replaced_teacher_id',$teacher->id)
            ->where('selected_date',$dayName->toDateString())
            ->get();
        $collectionBeAssignedSchedule = collect($assignedSchedules)->map(function($schedule){
            return $schedule->schedule_id;
        })->toArray();

        if($rows){
            foreach($rows as $row){
                $data = [
                    'id'=>$row->id,
                    'class' => $row->class_name,
                    'lesson'=> str_replace('\n','/',$row->subject_code),
                    'slot'=> [$row->slot_id],
                    'start'=> substr($row->start_time,0,-3),
                    'end'=> substr($row->end_time,0,-3),
                    'status'=>'unavaliable',
                    'content'=>'relif made',
                    'number'=> '99'
                ];
                if(in_array($row->id, $collectionSchedule) ){
                    array_push($pairs,$data);
//                    array_push($classes,$data);//need custom script js
                }
                else{
                    array_push($classes,$data);
                }

            }
            if(count($beAssignedSchedules) > 0){
                foreach($beAssignedSchedules as $beAssignedSchedule){
                    $data = [
                        'id'=>$beAssignedSchedule->schedule->id,
                        'class' => $beAssignedSchedule->schedule->class_name,
                        'lesson'=> str_replace('\n','/',$beAssignedSchedule->schedule->subject_code),
                        'slot'=> [$beAssignedSchedule->schedule->slot_id],
                        'start'=> substr($beAssignedSchedule->schedule->start_time,0,-3),
                        'end'=> substr($beAssignedSchedule->schedule->end_time,0,-3),
                        'status'=>'unavaliable',
                        'content'=>'relif made',
                        'number'=> '99'
                    ];
                    array_push($beAssigned,$data);
//                    array_push($classes,$data);
                }
            }
            $result['data']['time_data'][0]['required']['classes'] = $classes;
            $result['data']['time_data'][0]['required']['paired'] = $pairs;
            $result['data']['time_data'][0]['required']["substituted"] = [];
            $result['data']['time_data'][0]['required']['red'] = $beAssigned;

        }
        return $result;
    }

    public function getFreeUserWithSchedules($date, $eventIds, $type){
        $events = $eventIds;
        $optionRead = $type;
        $dayName = Carbon::parse($date);

        $result['data']['time_data'] = [];
        $status = 0;

        if(count($events)>0){
            DB::enableQueryLog();
            $whereData = [];
            $subQuery = '';
            $slotIds = [];

            if(count($events) == 1){
                $slot = $this->model->find($events[0]);
                array_push($whereData,$slot->slot_id);
                array_push($slotIds,$slot->slot_id);

                $subQuery .= 's.slot_id=?';
                $whereData[]= $slot->day_name;
                $whereData[]= $slot->teacher_id;
            }elseif(count($events) > 1){
                foreach($events as $k => $event){
                    $slot = $this->model->find($event);
                    array_push($whereData,$slot->slot_id);
                    array_push($slotIds,$slot->slot_id);

                    if($k==0){
                        $subQuery .= 's.slot_id=? OR ';
                    }
                    else{
                        if($k  == count($events) -1){
                            $subQuery .= 's.slot_id=?';

                            $whereData[]= $slot->day_name;
                            $whereData[]= $slot->teacher_id;
                        }else{
                            $subQuery .= 's.slot_id=? OR ';
                        }
                    }

                }
            }

//            DB::enableQueryLog();
            $userTimelines = DB::select('SELECT t.name,t.id as teacher_id  FROM 
	 ( SELECT * FROM makeit__teachers t WHERE NOT EXISTS( SELECT * FROM makeit__schedules s WHERE t.id = s.teacher_id AND ( '.$subQuery.') AND s.day_name=? ) ) t	
WHERE t.id != ?',$whereData);
//            dd(DB::getQueryLog());

//            $userTimelines = $query->groupBy('teacher_id')->get();
            if(!empty($userTimelines)){
//                $collection = collect($userTimelines);
//                $userTimelines = $collection->groupBy('name')->toArray();

                $status = 1;
                $i = 0;

                $query = Activity::where('teacher_id',$slot->teacher_id)
                    ->where('selected_date',$dayName->toDateString());
                if(count($events) > 1){
                    foreach($events as $scheduleId){
                        $query->where('schedule_id',$scheduleId);
                    }
                }
                else{
                    $query->where('schedule_id',$events[0]);
                }

                $assignedSchedules= $query->get();
                $collectionSchedule = collect($assignedSchedules)->map(function($schedule){
                    return $schedule->replaced_teacher_id;
                })->toArray();

                foreach($userTimelines as $key => $items) {
                    $teacherName = $items->name;
                    $teacherId = $items->teacher_id;

                    $schedulesByTeacher = Schedule::where('teacher_id',$teacherId)->where('day_name',$dayName->format('l'))->get();
//                    dd(DB::getQueryLog());
//                    dd($schedulesByTeacher);
                    if(count($schedulesByTeacher) > 0){
                        foreach($schedulesByTeacher as $item){
                            $result['data']['time_data'][$i]['required']['classes'][] = [
                                'slot'=>[$item->slot_id],
                                'lesson'=>str_replace('/',',',trim(preg_replace('/\r\n|\r|\n/', ',', $item->subject_code)))
                            ];
                        }
                    }
                    else{
                        $result['data']['time_data'][$i]['required']['classes'] =[];
                    }

                    $result['data']['time_data'][$i]['required']['teacher'] = $teacherName;
                    $result['data']['time_data'][$i]['required']['teacher_id'] = $teacherId;
                    $result['data']['time_data'][$i]['required']['status'] = '';
                    $result['data']['time_data'][$i]['required']['number'] = '';
                    if(in_array($teacherId, $collectionSchedule)){
                        $result['data']['time_data'][$i]['required']['content'] = 'Assigned';
                    }
                    else{
                        $result['data']['time_data'][$i]['required']['content'] = '';
                    }


                    $i++;

                }
            }
            else{

            }
        }
        else{
            //empty events
        }
        return $result;
    }

    public function replaceTeacher($schedules,$replaceTeacherId,$replaceDate){
        if(is_array($schedules)){
            foreach($schedules as $scheduleId){
                $selectedSchedule = $this->model->find($scheduleId);

                //delete first
                Activity::where('teacher_id',$selectedSchedule->teacher_id)
                    ->where('schedule_id',$scheduleId)
                    ->where('selected_date',Carbon::parse($replaceDate)->toDateString())
                    ->delete();


                Activity::create([
                    'teacher_id'=>$selectedSchedule->teacher_id,
                    'replaced_teacher_id'=>$replaceTeacherId,
                    'schedule_id'=>$scheduleId,
                    'selected_date'=> Carbon::parse($replaceDate)->toDateString(),
                    'status'=> Activity::ASSIGNED_STATUS
                ]);
            }
            return true;
        }
        return false;
    }

    public function getSchedulesInArray($ids = array()){
        return $this->model->whereIn('id',$ids)->get();
    }
}

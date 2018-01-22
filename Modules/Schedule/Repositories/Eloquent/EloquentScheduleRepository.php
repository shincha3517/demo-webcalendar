<?php

namespace Modules\Schedule\Repositories\Eloquent;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Core\Repositories\Eloquent\EloquentBaseRepository;
use Modules\Schedule\Entities\Activity;
use Modules\Schedule\Entities\Assignment;
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
            if($i== 31){
                $startDate = $startDate->addMinutes(75);
            }

            $startTime = substr($startDate->toTimeString(),0,-3);
            if($i== 30 || $i == 31){
                //hardcode
                $endTime   = substr($startDate->addMinutes(90)->toTimeString('h:m'),0,-3);
            }else{
                $endTime   = substr($startDate->addMinutes($interval)->toTimeString('h:m'),0,-3);
            }

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
        $assignedTeacher = Assignment::where('selected_date',$date->toDateString())->where('is_past',0)->get();
        if(count($assignedTeacher) > 0){
            foreach($assignedTeacher as $row){
                $i=0;
                foreach($result as $rs){
                    if($rs['id'] == $row->replaced_teacher_id){
                        break;
                    }
                    else{
                        if($i == count($result) -1){
                            $teacher = [
                                'id'=>$row->replaced_teacher_id ? $row->replaced_teacher_id:0,
                                'text'=>$row->replaceTeacher ? $row->replaceTeacher->name: '',
                            ];
                            array_push($result,$teacher);
                        }
                    }
                    $i++;
                }
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

        $query->orderBy('slot_id', 'asc');
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
        $substituted = [];

        $assignedSchedules = Assignment::where('teacher_id',$teacher->id)
            ->where('selected_date',$dayName->toDateString())
            ->where('is_past',0)
            ->orderBy('slot_id', 'asc')
            ->get();
        $collectionSchedule = collect($assignedSchedules)->map(function($schedule){
            return $schedule->schedule_id;
        })->toArray();

        $beAssignedSchedules = Assignment::where('replaced_teacher_id',$teacher->id)
            ->where('selected_date',$dayName->toDateString())
            ->where('is_past',0)
            ->where('schedule_type','old')
            ->orderBy('slot_id', 'asc')
            ->get();
        $collectionBeAssignedSchedule = collect($assignedSchedules)->map(function($schedule){
            return $schedule->schedule_id;
        })->toArray();

        if($rows){
            foreach($rows as $row){

                $sameClass = $this->getByAttributes(['class_name' => $row->class_name,'date_id'=>$row->date_id])->toArray();
//                print_r($sameClass);exit;

                $data = [
                    'id'=>$row->id,
                    'class' => $row->class_name,
                    'lesson'=> str_replace('\n','/',$row->subject_code),
                    'slot'=> [$row->slot_id],
                    'start'=> substr($row->start_time,0,-3),
                    'end'=> substr($row->end_time,0,-3),
                    'status'=>'unavaliable',
                    'content'=>'relif made',
                    'number'=> '99',
                    'flag' => 'classes'
                ];
                if(in_array($row->id, $collectionSchedule) ){
                    $data['flag'] = 'paired';
//                    array_push($pairs,$data);
//                    array_push($classes,$data);//need custom script js
                }
                elseif(count($sameClass) > 1){
                    $data['flag'] = 'substituted';
//                    array_push($substituted,$data);
                }
                else{
//                    array_push($classes,$data);
                }
                array_push($classes,$data);

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
                    $data['flag'] = 'red';
                    array_push($classes,$data);
//                    array_push($classes,$data);
                }
            }
            $result['data']['time_data'][0]['required']['classes'] = $classes;
//            $result['data']['time_data'][0]['required']['paired'] = $pairs;
//            $result['data']['time_data'][0]['required']["substituted"] = $substituted;
//            $result['data']['time_data'][0]['required']['red'] = $beAssigned;

        }
        return $result;
    }

    public function getFreeUserWithSchedules($date, $eventIds, $type, $sortingType){
        $events = $eventIds;
        $optionRead = $type;
        $dayName = Carbon::parse($date);

        $result['data']['time_data'] = [];
        $status = 0;

        if(count($events)>0){
//            DB::enableQueryLog();
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
//                $whereData[]= $slot->teacher->subject;
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
//                            $whereData[]= $slot->teacher->subject;
                        }else{
                            $subQuery .= 's.slot_id=? OR ';
                        }
                    }

                }
            }

//            DB::enableQueryLog();
            $userTimelines = DB::select('SELECT t.name,t.id as teacher_id, t.subject  FROM 
	 ( SELECT * FROM makeit__teachers t WHERE NOT EXISTS( SELECT * FROM makeit__schedules s WHERE t.id = s.teacher_id AND ( '.$subQuery.') AND s.day_name=? ) ) t	
WHERE t.id != ?',$whereData);
//            dd(DB::getQueryLog());

//            $userTimelines = $query->groupBy('teacher_id')->get();
            if(!empty($userTimelines)){
//                $collection = collect($userTimelines);
//                $userTimelines = $collection->groupBy('name')->toArray();

                $status = 1;
                $i = 0;


                $scheduleRelief = Schedule::find($events[0]);
                $subject = $scheduleRelief->teacher->subject;

                $result['subject'] = $subject;



                $query = Assignment::where('selected_date',$dayName->toDateString())->where('is_past',0);
                if(count($events) > 1){
                    $slotArray = [];
                    foreach($events as $scheduleId){
                        $slot = $this->model->find($scheduleId);
                        array_push($slotArray,$slot->slot_id);
                    }
                    $query->where('schedule_type','old');
                    $query->whereIn('slot_id',$slotArray);
                }
                else{
                    $slot = $this->model->find($events[0]);

                    $query->where('schedule_type','old');
                    $query->where('slot_id',$slot->slot_id);
                }

                $assignedSchedules= $query->get();
                $collectionSchedule = collect($assignedSchedules)->map(function($schedule){
                    return $schedule->replaced_teacher_id;
                })->toArray();

                foreach($userTimelines as $key => $items) {
                    $teacherName = $items->name;
                    $teacherId = $items->teacher_id;

                    if(in_array($teacherId, $collectionSchedule)){
                        unset($userTimelines[$key]);
                        continue;
                    }

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

                    $reliefSchedule = Assignment::where('replaced_teacher_id', $teacherId)->where('is_past',0)->whereDate('selected_date',$dayName->toDateString())->where('status',1)->get();
                    if(count($reliefSchedule)){
                        foreach($reliefSchedule as $item){
                            array_push($result['data']['time_data'][$i]['required']['classes'],[
                                'slot'=>[$item->slot_id],
                                'lesson'=>str_replace('/',',',trim(preg_replace('/\r\n|\r|\n/', ',', $item->subject)))
                            ]);
                        }
                    }

                    $lessons = Schedule::where('teacher_id',$teacherId)->where('day_name',$dayName->format('l'))->count();
                    $assignLessons = Assignment::where('replaced_teacher_id', $teacherId)->where('is_past',0)->whereDate('selected_date',$dayName->toDateString())->count();

                    $totalLessons = $lessons + $assignLessons;


                    $result['data']['time_data'][$i]['required']['teacher'] = $teacherName;
                    $result['data']['time_data'][$i]['required']['teacher_id'] = $teacherId;
                    $result['data']['time_data'][$i]['required']['status'] = '';
                    $result['data']['time_data'][$i]['required']['number'] = $totalLessons;
                    $result['data']['time_data'][$i]['required']['content'] = $items->subject;


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

    public function replaceTeacher($schedules,$replaceTeacherId,$replaceDate,$reason,$additionalRemark,$notifyInterval){
        if(is_array($schedules)){
            $selectedDate = Carbon::parse($replaceDate)->toDateString();
            $jobsCode = DB::table('makeit__assignment')->max('code');
            $jobsCode = $jobsCode+1;

            $pad_length = 4;
            $pad_char = 0;
            $jobsCode = str_pad($jobsCode, $pad_length, $pad_char, STR_PAD_LEFT);

            //notify config
            switch ($notifyInterval){
                case '15':
                    $notifyAt = Carbon::now()->addMinutes(15)->toDateTimeString();
                    break;
                case '30':
                    $notifyAt = Carbon::now()->addMinutes(30)->toDateTimeString();
                    break;
                case '1':
                    $notifyAt = Carbon::now()->addHour(1)->toDateTimeString();
                    break;
                case '3':
                    $notifyAt = Carbon::now()->addHours(3)->toDateTimeString();
                    break;
                default:
                    $notifyAt = Carbon::now()->addMinutes(15)->toDateTimeString();
            }


            foreach($schedules as $scheduleId){
                $selectedSchedule = $this->model->find($scheduleId);
                $replacedTeacher = Teacher::find($replaceTeacherId);
                //delete first
//                Activity::where('teacher_id',$selectedSchedule->teacher_id)
//                    ->where('schedule_id',$scheduleId)
//                    ->where('selected_date',Carbon::parse($replaceDate)->toDateString())
//                    ->delete();


                Activity::create([
                    'teacher_id'=>$selectedSchedule->teacher_id,
                    'replaced_teacher_id'=>$replaceTeacherId,
                    'schedule_id'=>$scheduleId,
                    'selected_date'=> $selectedDate,
                    'status'=> Activity::ASSIGNED_STATUS
                ]);

                Assignment::create([
                    'teacher_id'=>$selectedSchedule->teacher_id,
                    'replaced_teacher_id'=>$replaceTeacherId,
                    'teacher_name'=> $selectedSchedule->teacher->name,
                    'replaced_teacher_name'=> $replacedTeacher->name,

                    'schedule_id'=>$scheduleId,
                    'lesson'=>$selectedSchedule->class_name,
                    'subject'=>$selectedSchedule->subject_code,
                    'start_date'=> $selectedSchedule->start_date,
                    'end_date'=> $selectedSchedule->end_date,
                    'slot_id'=>$selectedSchedule->slot_id,
                    'day_name'=>$selectedSchedule->day_name,
                    'selected_date'=>$selectedDate,
                    'reason'=>$reason,
                    'additionalRemark'=>$additionalRemark,
                    'schedule_type'=>'old',
                    'code'=>$jobsCode,
                    'created_by'=> auth()->user()->email,
                    'notify_at'=>$notifyAt,
                    'notify_status'=> 0,
                ]);
            }
            return $jobsCode;
        }
        return false;
    }

    public function getSchedulesInArray($ids = array()){
        return $this->model->whereIn('id',$ids)->get();
    }

    public function userCancelAssignSchedule($scheduleId,$date){
        $selectedDate = Carbon::parse($date)->toDateString();
        $activity = Activity::where('schedule_id',$scheduleId)->where('selected_date',$selectedDate)->get()->first();

        $cancelActivity = new Activity();
        $cancelActivity->teacher_id = $activity->teacher_id;
        $cancelActivity->replaced_teacher_id = $activity->replaced_teacher_id;
        $cancelActivity->schedule_id = $activity->schedule_id;
        $cancelActivity->selected_date = $activity->selected_date;
        $cancelActivity->status = 2;
//        $cancelActivity->save();

        //Assignment
        $assignment = Assignment::where('schedule_id',$scheduleId)->where('selected_date',$selectedDate)->where('is_past',0)->delete();
    }

    public function createAbsentRequest($teacherId,$replaceTeacherId,$replaceDate,$reason,$additionalRemark,$startDate,$endDate,$absentType,$scheduleId){
        $selectedDate = Carbon::parse($replaceDate)->toDateString();
        $jobsCode = DB::table('makeit__assignment')->max('code');
        $jobsCode = $jobsCode+1;

        $pad_length = 4;
        $pad_char = 0;
        $jobsCode = str_pad($jobsCode, $pad_length, $pad_char, STR_PAD_LEFT);

        $teacher = Teacher::find($teacherId);
        $replaceTeacher = Teacher::find($replaceTeacherId);

        $selectedSchedule = $this->model->find($scheduleId);

        Assignment::create([
            'teacher_id'=>$teacher->id,
            'replaced_teacher_id'=>$replaceTeacher->id,
            'teacher_name'=> $teacher->name,
            'replaced_teacher_name'=> $replaceTeacher->name,
            'schedule_id'=>$scheduleId,
            'start_date'=> $startDate,
            'end_date'=> $endDate,
            'slot_id'=>$selectedSchedule ? $selectedSchedule->slot_id : null,
            'lesson'=>$selectedSchedule ? $selectedSchedule->class_name : null,
            'subject'=>$absentType,
            'selected_date'=>$selectedDate,
            'reason'=>$reason,
            'additionalRemark'=>$additionalRemark,
            'schedule_type'=>'absent',
            'schedule_type'=>'old',
            'code'=>$jobsCode,
            'created_by'=> auth()->user()->email,
            'notify_at'=>null,
            'notify_status'=> 1,
        ]);

        return $jobsCode;
    }
}

<?php

namespace Modules\Schedule\Http\Controllers\Admin;

use Carbon\Carbon;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Core\Http\Controllers\Admin\AdminBaseController;
use Modules\Schedule\Entities\Schedule;
use Modules\Schedule\Entities\ScheduleDate;
use Modules\Schedule\Entities\Teacher;
use Modules\Schedule\Events\ImportExcelSchedule;
use Modules\Schedule\Http\Requests\UploadExcelRequest;
use Modules\Schedule\Repositories\ScheduleRepository;
use Modules\Schedule\Repositories\TeacherRepository;
use Modules\User\Contracts\Authentication;
use Modules\User\Permissions\PermissionManager;
use Modules\User\Repositories\RoleRepository;
use Modules\User\Repositories\UserRepository;
use Nexmo\Laravel\Facade\Nexmo;

class ScheduleController extends AdminBaseController
{
    use ValidatesRequests;
    /**
     * @var Authentication
     */
    private $auth;

    protected $teacherRepository;
    protected $scheduleRepository;

    /**
     * @param PermissionManager $permissions
     * @param UserRepository    $user
     * @param RoleRepository    $role
     * @param Authentication    $auth
     */
    public function __construct(
        Authentication $auth,
        TeacherRepository $teacherRepository,
        ScheduleRepository $scheduleRepository
    ) {
        parent::__construct();

        $this->auth = $auth;
        $this->teacherRepository = $teacherRepository;
        $this->scheduleRepository = $scheduleRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $currentUser = $this->auth->user();
        $teachers = $this->teacherRepository->all();

        return view('schedule::admin.schedule.admin_schedule', compact('currentUser','teachers'));
    }

    public function getUpload(){
        return view('schedule::admin.schedule.upload', compact(''));
    }

    public function getSyncData(Request $request){
        dd($request->all());
        $current = $request->get("current")+1;
        $steps = 100;

        echo json_encode(array("step" => $current, "total" => $steps, "label" => "server completed ". $current . " of " . $steps));
    }

    public function doUpload(UploadExcelRequest $request){

        $interval = $request->get('interval');
        $startTime = $request->get('startTime');

        $file = $request->file('importedFile');
        $file->move(storage_path('imports'), 'import.' . $file-> getClientOriginalExtension());

        $path = storage_path('imports')."/import.xlsx";

        $objPHPExcel = \PHPExcel_IOFactory::load($path);
        $objWorksheet = $objPHPExcel->getActiveSheet();
        $highestRow = $objWorksheet->getHighestRow();
//        $highestRow = 10;
        $limitRow = 10;

        $limitRunRow = $highestRow / $limitRow;

//        dd(Carbon::now()->toDateString());

        $daysInWeek = ['Monday','Tuesday','Wednesday','Thursday','Friday'];
        $hoursInDays = 17;
        //update schedule
        $week = $this->_get_current_week();
        //
        $mergeCells = $objWorksheet->getMergeCells();

        DB::table('makeit__schedules')->delete();
        DB::table('makeit__teachers')->delete();
        DB::table('makeit__schedule_dates')->delete();

        //
        $date = Carbon::now()->toDateString();
        $scheduleDate = Carbon::createFromFormat('Y-m-d g:ia',$date.' '.$startTime)->toDateTimeString();

        ScheduleDate::create([
           'date'=>$scheduleDate,
            'start_date'=>$scheduleDate,
            'day_name'=> Carbon::today()->format('D'),
            'interval'=> $interval
        ]);


        for ($row = 1; $row <= $limitRunRow; $row++) {

            Log::info('=====start processing row ' . $row . '=========');
            event(new ImportExcelSchedule($path, $limitRow, $row,$interval,$startTime));
//            $this->import($path, $limitRow, $row,$interval,$startTime);
            Log::info('=====end processing row ' . $row . '=========');
        }

        $request->session()->flash('success','Upload excel file successfully');
        return redirect()->back();
    }
    private function _importExcelSchedule($path,$limitRow,$limitRunRow){


        $objPHPExcel = \PHPExcel_IOFactory::load($path);
        $objWorksheet = $objPHPExcel->getActiveSheet();

        $startRow = ($limitRow * $limitRunRow) > $limitRow ? ($limitRow * $limitRunRow) - $limitRow +1 : 0;
        $endRow = $limitRow * $limitRunRow;

        $daysInWeek = ['Monday','Tuesday','Wednesday','Thursday','Friday'];
        $hoursInDays = 17;

        $result = [];

        for ($row = $startRow; $row <= $endRow; ++$row) {
            Log::info('***start processing row ' . $row.'***');
            $teacherName = $objWorksheet->getCellByColumnAndRow(0 , $row)->getValue();
            $scheduleRow = [];
            if(!empty($teacherName)){
                $n=0;

                //check exist teacher name
                $teacherObject = $this->teacherRepository->findByAttributes(['name'=>$teacherName]);
                if(!$teacherObject){
                    $teacherObject = $this->teacherRepository->create(['name'=>$teacherName]);
                }

                for($j=1; $j<=$hoursInDays*count($daysInWeek);$j++){
//                        $column = $objWorksheet->getCellByColumnAndRow($j,$row)->getColumn();
                    $cellNum = $objWorksheet->getCellByColumnAndRow($j,$row)->getRow();
//                        $cell = $objWorksheet->getCell($column.$cellNum);

                    if (!$objWorksheet->getCellByColumnAndRow( $j,$row)->isInMergeRange() || $objWorksheet->getCellByColumnAndRow( $j,$row )->isMergeRangeValueCell()) {
                        $scheduleRow[$daysInWeek[$n]][$j] = $objWorksheet->getCellByColumnAndRow($j , $row)->getValue();
                    } else {
//                            for($k=0; $k<=100 ; $k++){
//                                if($objWorksheet->getCellByColumnAndRow($j-$k , $row)->getValue() != null){
//                                    $scheduleRow[$daysInWeek[$n]][$j] = $objWorksheet->getCellByColumnAndRow($j-$k , $row)->getValue();
//                                    break;
//                                }
//                            }

                    }
                    if($j%$hoursInDays==0){
                        $n++;
                    }
                }
                foreach($scheduleRow as $day => $srow){

                    foreach($srow as $key => $value ){
                        if($value == null){
//                                unset($scheduleRow[$key]);
                        }
                        else{
                            $values = explode('\n',$value);
                            if(count($values) > 0){
                                for($v = 0; $v < count($values); $v++){
                                    $scheduleData = [
                                        'teacher_id'=>$teacherObject->id,
                                        'subject_code'=>$values[$v],
                                        'date_id'=> $key
                                    ];
                                    $this->scheduleRepository->create($scheduleData);
                                }
                            }
                            else{
                                $scheduleData = [
                                    'teacher_id'=>$teacherObject->id,
                                    'subject_code'=>$value,
                                    'date_id'=> $key
                                ];
                                $this->scheduleRepository->create($scheduleData);
                            }


                        }
                    }
                }
            }
            else{
                //teacher name blank
            }
            Log::info('***end processing row ' . $row.'***');
        }
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function import($path, $limitRow, $row,$interval,$startTime)
    {
        $path = $path;
        $limitRow = $limitRow;
        $limitRunRow = $row;
        $interval = $interval;
        $startTime = $startTime;

        $objPHPExcel = \PHPExcel_IOFactory::load($path);
        $objWorksheet = $objPHPExcel->getActiveSheet();

        $startRow = ($limitRow * $limitRunRow) > $limitRow ? ($limitRow * $limitRunRow) - $limitRow +1 : 0;
        $endRow = $limitRow * $limitRunRow;

        $daysInWeek = ['Monday','Tuesday','Wednesday','Thursday','Friday'];
        $hoursInDays = 17;

        $result = [];

        for ($row = $startRow; $row <= $endRow; ++$row) {
            Log::info('***INSERT' . $row . '***');
            $teacherName = $objWorksheet->getCellByColumnAndRow(0, $row)->getValue();
            $scheduleRow = [];
            if (!empty($teacherName)) {
                $n = 0;
                $m = 1;

                //check exist teacher name
                $teacherObject = $this->teacherRepository->findByAttributes(['name' => $teacherName]);
                if (!$teacherObject) {
                    $teacherObject = $this->teacherRepository->create(['name' => $teacherName]);
                }

                for ($j = 1; $j <= $hoursInDays * count($daysInWeek); $j++) {
//                    $column = $objWorksheet->getCellByColumnAndRow($j,$row)->getColumn();
                    $cellNum = $objWorksheet->getCellByColumnAndRow($j, $row)->getRow();

//                        $cell = $objWorksheet->getCell($column.$cellNum);

                    if (!$objWorksheet->getCellByColumnAndRow($j, $row)->isInMergeRange() || $objWorksheet->getCellByColumnAndRow($j, $row)->isMergeRangeValueCell()) {
                        $scheduleRow[$daysInWeek[$n]][$j] = $objWorksheet->getCellByColumnAndRow($j, $row)->getValue();
                    } else {
                        $mergeRange = $objWorksheet->getCellByColumnAndRow($j , $row)->getMergeRange();
                        $mergeRangeArray = explode(':',$mergeRange);
                        $scheduleRow[$daysInWeek[$n]][$j] = $objWorksheet->getCell($mergeRangeArray[0])->getValue();

                    }

                    $dateSchedules[$j] = $this->getDateSchedule($j, $m, $interval, $startTime);

                    if ($j % $hoursInDays == 0) {
                        $n++;
                        $m = 1;
                    } else {
                        $m++;
                    }

                }
                foreach ($scheduleRow as $day => $srow) {
                    $e = 1;
                    foreach ($srow as $key => $value) {
                        if ($value == null) {
//                                unset($scheduleRow[$key]);
                        } else {
                            $values = explode('\n', $value);
                            $startDate = $dateSchedules[$key];
                            $endDate = Carbon::parse($startDate)->addMinutes(30);

                            $scheduleStartTime = Carbon::parse($startDate)->toTimeString();
                            $scheduleEndTime = Carbon::parse($endDate)->toTimeString();

                            if (count($values) > 0) {
                                for ($v = 0; $v < count($values); $v++) {
                                    $scheduleData = [
                                        'teacher_id' => $teacherObject->id,
                                        'subject_code' => $values[$v],
                                        'date_id' => $key,
                                        'slot_id' => $e,
                                        'start_date' => $startDate,
                                        'end_date' => $endDate,
                                        'start_time' => $scheduleStartTime,
                                        'end_time' => $scheduleEndTime
                                    ];
                                    $this->scheduleRepository->create($scheduleData);
                                }
                            } else {
                                $scheduleData = [
                                    'teacher_id' => $teacherObject->id,
                                    'subject_code' => $value,
                                    'date_id' => $key,
                                    'slot_id' => $e,
                                    'start_date' => $startDate,
                                    'end_date' => $endDate,
                                    'start_time' => $scheduleStartTime,
                                    'end_time' => $scheduleEndTime
                                ];
                                $this->scheduleRepository->create($scheduleData);
                            }


                        }
                        if ($key % $hoursInDays == 0) {
                            $e = 1;
                        } else {
                            $e++;
                        }
                    }
                }
            } else {
                //teacher name blank
            }
            Log::info('***End insert row ' . $row . '***');
        }
    }
    private function _get_current_week()
    {
        // set current timestamp
        $today = time();
        $w = array();
        // calculate the number of days since Monday
                $dow = date('w', $today);
        $offset = $dow - 1;
        if ($offset < 0) {
            $offset = 6;
        }
        // calculate timestamp from Monday to Sunday
        $monday = $today - ($offset * 86400);
        $tuesday = $monday + (1 * 86400);
        $wednesday = $monday + (2 * 86400);
        $thursday = $monday + (3 * 86400);
        $friday = $monday + (4 * 86400);
        $saturday = $monday + (5 * 86400);
        $sunday = $monday + (6 * 86400);

        $format = 'Y-m-d';

        // return current week array
        $w['monday '] = Carbon::createFromTimestamp($monday)->format($format) ;
        $w['tuesday '] = Carbon::createFromTimestamp($tuesday)->format($format) ;
        $w['wednesday'] = Carbon::createFromTimestamp($wednesday)->format($format);
        $w['thursday '] = Carbon::createFromTimestamp($thursday)->format($format) ;
        $w['friday '] = Carbon::createFromTimestamp($friday)->format($format) ;
        $w['saturday '] = Carbon::createFromTimestamp($saturday)->format($format) ;
        $w['sunday '] = Carbon::createFromTimestamp($sunday)->format($format) ;
        ;
        return $w;
    }

    public function getUserByDate(Request $request){
        $date = $request->get('date');
        $date = Carbon::createFromFormat('m/d/Y',$date)->toDateString();
        $dayName = Carbon::parse($date);

//        DB::enableQueryLog();
//        $rows = $this->scheduleRepository->getByAttributes(['start_date'=>$date]);
        $query = Schedule::with('teacher');
        if($dayName->isMonday()){
            $query->where('day_name','Monday');
        }elseif($dayName->isTuesday()){
            $query->where('day_name','Tuesday');
        }elseif($dayName->isWednesday()){
            $query->where('day_name','Wednesday');
        }elseif($dayName->isThursday()){
            $query->where('day_name','Thursday');
        }elseif($dayName->isFriday()){
            $query->where('day_name','Friday');
        }
        else{
            $query->whereNull('day_name');
        }
        $query->groupBy('teacher_id');

        $rows = $query->get();
//        dd(DB::getQueryLog());
        $result = [];
        if(count($rows) > 0){
            foreach($rows as $row){
                $teacher = [
                  'id'=>$row->teacher->id,
                  'text'=>$row->teacher->name,
                ];
                array_push($result,$teacher);
            }
            return response()->json(['result'=>$result,'status'=>1]);
        }
        else{
            return response()->json(['result'=>[],'status'=>0]);
        }

    }

//    public function getUserTimeline(Request $request){
//        $teacherId = $request->get('teacher_id');
//        $teacher = $this->teacherRepository->find($teacherId);
//        $paramDate = $request->get('date');
//        $startDate = Carbon::createFromFormat('m/d/Y',$paramDate)->toDateString();
//        $dayName = Carbon::parse($request->get('date'));
//
//        $min = Carbon::createFromFormat('m/d/Y',$paramDate)->hour(0)->minute(0)->second(0)->format('Y-m-d\TH:i:s');
//        $max = Carbon::createFromFormat('m/d/Y',$paramDate)->hour(23)->minute(59)->second(59)->format('Y-m-d\TH:i:s');
//
////        $rows = $this->scheduleRepository->getByAttributes(['teacher_id'=>$teacherId,'start_date'=>$startDate]);
//        $query = Schedule::where('teacher_id',$teacherId);
//
//        $dayNameData = '';
//        if($dayName->isMonday()){
//            $query->where('day_name','Monday');
//            $dayNameData = 'Monday';
//        }elseif($dayName->isTuesday()){
//            $query->where('day_name','Tuesday');
//            $dayNameData = 'Tuesday';
//        }elseif($dayName->isWednesday()){
//            $query->where('day_name','Wednesday');
//            $dayNameData = 'Wednesday';
//        }elseif($dayName->isThursday()){
//            $query->where('day_name','Thursday');
//            $dayNameData = 'Thursday';
//        }elseif($dayName->isFriday()){
//            $query->where('day_name','Friday');
//            $dayNameData = 'Friday';
//        }else{
//            $query->whereNull('day_name');
//            $dayNameData = '';
//        }
//
//        $query->groupBy('date_id');
//        $rows= $query->get();
//
////        $rows = DB::select(DB::raw("SELECT
////	s.id,s.teacher_id,s.date_id,s.subject_code, tbl.start_time, tbl_max.end_time
////FROM
////	makeit__schedules s
////INNER JOIN(
////	SELECT
////		subject_code ,
////		min(start_time) AS start_time
////	FROM
////		makeit__schedules
////	WHERE
////		teacher_id = $teacherId
////	AND day_name = '$dayNameData'
////	GROUP BY
////		subject_code
////) tbl ON s.subject_code = tbl.subject_code
////INNER JOIN(
////	SELECT
////		subject_code ,
////		max(end_time) AS end_time
////	FROM
////		makeit__schedules
////	WHERE
////		teacher_id = $teacherId
////	AND day_name = '$dayNameData'
////	GROUP BY
////		subject_code
////) tbl_max ON s.subject_code = tbl_max.subject_code GROUP BY subject_code"));
//
//        $result = [];
//        $group[] = [
//            'id'=>$teacherId,
//            'content'=> $teacher->name.'&nbsp;&nbsp;&nbsp;',
//            'value'=>$teacherId
//        ];
//        if($rows){
//            foreach($rows as $row){
//                $data = [
//                    'id'=>$row->id,
//                    'group'=>$teacherId,
//                    'content'=> $row->subject_code,
//                    'start'=>Carbon::parse($paramDate)->setTimeFromTimeString($row->start_time)->format('Y-m-d\TH:i:s'),
//                    'end'=>Carbon::parse($paramDate)->setTimeFromTimeString($row->end_time)->format('Y-m-d\TH:i:s'),
//                ];
//                array_push($result,$data);
//            }
//            return response()->json(['result'=>$result,'status'=>1,'min'=>$min,'max'=>$max,'group'=>$group]);
//        }else{
//            return response()->json(['result'=>$result,'status'=>0,'min'=>$min,'max'=>$max,'group'=>$group]);
//        }
//
//    }
//    public function getAvailableUser(Request $request){
//        $event = $request->get('eventId');
//
//        $eventItem = $this->scheduleRepository->find($event);
//        $timeSlotId = $eventItem->date_id;
//        $startDate = $eventItem->start_date;
//
////        $availableUser = Teacher::whereHas('schedule', function($q) use ($startDate){
////            $q->whereDate('start_date','!=',$startDate);
////        })->get()->toArray();
//
//
//        $teachers = Teacher::where('id','!=',$eventItem->teacher_id)->get()->toArray();
//        $availableTeachers = $teachers;
//        $busyTeachers = Schedule::where('teacher_id','!=',$eventItem->teacher_id)
////            ->whereBetween('start_date',[$eventItem->start_date,$eventItem->end_date])
//            ->whereDate('start_date',$eventItem->start_date)
//            ->where('date_id',$timeSlotId)
//            ->groupBy('teacher_id')->get();
//
//        if(count($busyTeachers) > 0){
//            foreach($busyTeachers as $teacher){
//                $teacherId = $teacher->teacher_id;
//                for($i = 0; $i < count($teachers); $i++){
//                    if($teachers[$i]['id'] == $teacherId){
//                        unset($availableTeachers[$i]);
//                    }
//                }
//            }
//            $availableTeachers = array_values($availableTeachers);
//        }
//
//
//
//        $users = [];
//        $timelines = [];
//        $expand = [];
//        if(count($availableTeachers) > 0){
//            $i=0;
//            foreach($availableTeachers as $user){
//                $row = [
//                    'id'=>$user['id'],
//                    'content'=>$user['name'],
//                    'value'=>$user['id'],
//                ];
//                array_push($users,$row);
//                $i++;
//            }
//        }
//
//        if(count($users)>0){
//            $date = Carbon::parse($eventItem->start_date)->format('Y-m-d');
//            foreach ($users as $key=> $user){
//                //get timelines foreach user
//                DB::enableQueryLog();
//                $userTimelines = Schedule::where('teacher_id',$user['id'])
//                        ->where('date_id','>',$timeSlotId)
//                    ->whereDate('start_date',$date)->get();
////                    ->whereDate('start_date','!=',$eventItem->start_date)->get()->first();
//
//
////                dd(DB::getQueryLog());
////                $item = [
////                    'id'=>$eventItem->id,
////                    'group'=>$user['id'],
////                    'content'=> $eventItem->subject_code,
////                    'start'=> Carbon::parse($eventItem->start_date)->format('Y-m-d\TH:i:s'),
////                    'end'=> Carbon::parse($eventItem->end_date)->format('Y-m-d\TH:i:s'),
////                ];
//
//                if($userTimelines){
//                    foreach($userTimelines as $k => $uTimeline) {
//
//                        if($k==0){
//                            $item = [
//                                'id' => $eventItem->id.'_'.uniqid(),
//                                'group' => $user['id'],
//                                'content' => '',
//                                'start' => Carbon::parse($eventItem->start_date)->format('Y-m-d\TH:i:s'),
//                                'end' => Carbon::parse($eventItem->end_date)->format('Y-m-d\TH:i:s'),
//                                'className'=> 'orange',
//                            ];
//                            array_push($timelines, $item);
//                        }
//                        $item = [
//                            'id' => $uTimeline->id,
//                            'group' => $user['id'],
//                            'content' => $uTimeline->subject_code,
//                            'start' => Carbon::parse($uTimeline->start_date)->format('Y-m-d\TH:i:s'),
//                            'end' => Carbon::parse($uTimeline->end_date)->format('Y-m-d\TH:i:s'),
//                        ];
//
//                        array_push($timelines, $item);
//                    }
//                }
//            }
//        }
//
//
//        $min = Carbon::parse($eventItem->start_date)->hour(0)->minute(0)->second(0)->format('Y-m-d\TH:i:s');
//        $max = Carbon::parse($eventItem->end_date)->hour(23)->minute(59)->second(59)->format('Y-m-d\TH:i:s');
//
//        $result = [
//            'users' => $users,
//            'timelines'=>$timelines
//        ];
//        return response()->json(['result'=>$result,'status'=>1,'event'=>$eventItem,'min'=>$min,'max'=>$max]);
//    }

    public function sendNotification(Request $request){

        //update schedule
        $schedules = $request->get('schedules');
        $replaceTeacherId = $request->get('replaceTeacher');
        if(is_array($schedules)){
            foreach($schedules as $scheduleId){
                $selectedSchedule = $this->scheduleRepository->find($scheduleId);
                $selectedSchedule->teacher_id = $replaceTeacherId;
                $selectedSchedule->save();
            }
        }

        $body = $request->get('msg_body');
        if($replaceTeacherId){
            $phoneNumber = env('DEFAULT_PHONENUMBER');
            $from = env('DEFAULT_PHONENUMBER');

            Nexmo::message()->send([
                'to' => $phoneNumber,
                'from' => $from,
                'text' => $body
            ]);
            $request->session()->flash('success','Send SMS successfully');
        }
        else{
            $request->session()->flash('error','Send SMS error');
        };
        return redirect()->back();

    }

    public function getUserByEvent(Request $request){
        $event = $this->scheduleRepository->find($request->get('eventId'));
//        dd($request->get('eventId'));
        $user = $this->teacherRepository->find($event->teacher_id);

        $result = [
            'status'=>1,
            'result'=> [
                ['id'=>$user->id,'text'=>$user->name]
            ]
        ];
        return response()->json($result);
    }

    public function actionWorker(){
        $currentUser = $this->auth->user();
        $teacher = Teacher::get()->first();
        $teachers = $this->teacherRepository->all();

        return view('schedule::admin.schedule.worker', compact('currentUser','teachers','teacher'));
    }

    private function getDateSchedule($rowNo, $resetRowNo, $interval , $startTime){

        $firstDayOfWeek = Carbon::now()->startOfWeek();
        $format = 'Y-m-d';
        $full_format = 'Y-m-d h:m:s';

        $monday = $firstDayOfWeek->toDateString();
        $tuesday = Carbon::parse($monday)->addDay(1)->toDateString();
        $wednesday = Carbon::parse($monday)->addDays(2)->toDateString();
        $thursday = Carbon::parse($monday)->addDays(3)->toDateString();
        $friday = Carbon::parse($monday)->addDays(4)->toDateString();


        $result = '';
        if($rowNo >=1 && $rowNo <=17){
            $result = Carbon::createFromFormat('Y-m-d g:ia',$monday.' '.$startTime);
        }
        if($rowNo >17 && $rowNo <=34){
            $result = Carbon::createFromFormat('Y-m-d g:ia',$tuesday.' '.$startTime);
        }
        if($rowNo > 34 && $rowNo <=51){
            $result = Carbon::createFromFormat('Y-m-d g:ia',$wednesday.' '.$startTime);
        }
        if($rowNo >51  && $rowNo <=68){
            $result = Carbon::createFromFormat('Y-m-d g:ia',$thursday.' '.$startTime);
        }
        if($rowNo >68 && $rowNo <=85){
            $result = Carbon::createFromFormat('Y-m-d g:ia',$friday.' '.$startTime);
        }
        if($resetRowNo > 1 ){
            $result = $result->addMinutes( ($interval*$resetRowNo)-$interval );
        }
        return $result;
    }

    public function getAvailableUserByEvents2(Request $request){
        $events = $request->get('eventIds');
        $date = $request->get('date');
        $dayName = Carbon::parse($date);

        $result['data']['time_data'] = [];
        $status = 0;

        if(count($events)>0){
            DB::enableQueryLog();
            $whereData = [];
            $subQuery = '';

            if(count($events) == 1){
                $slot = $this->scheduleRepository->find($events[0]);
                array_push($whereData,$slot->slot_id);

                $subQuery .= 's.slot_id=?';
                $whereData[]= $slot->day_name;
                $whereData[]= $slot->teacher_id;
            }elseif(count($events) > 1){
                foreach($events as $k => $event){
                    $slot = $this->scheduleRepository->find($event);
                    array_push($whereData,$slot->slot_id);



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
            $userTimelines = DB::select('SELECT t.name,t.id as teacher_id,s.*  FROM 
	 ( SELECT * FROM makeit__teachers t WHERE NOT EXISTS( SELECT * FROM makeit__schedules s WHERE t.id = s.teacher_id AND ( '.$subQuery.') ) ) t
	LEFT JOIN makeit__schedules s ON t.id = s.teacher_id
WHERE s.day_name = ?
 AND s.teacher_id != ?',$whereData);
//            dd(DB::getQueryLog());

//            $userTimelines = $query->groupBy('teacher_id')->get();
            if(!empty($userTimelines)){
                $collection = collect($userTimelines);
                $userTimelines = $collection->groupBy('name')->toArray();

                $status = 1;
                $i = 0;
                foreach($userTimelines as $teacher => $items) {

                    foreach($items as $item){
                        $result['data']['time_data'][$i]['required']['classes'][] = [
                            'slot'=>[$item->slot_id],
                            'lesson'=>str_replace('/',',',trim(preg_replace('/\r\n|\r|\n/', ',', $item->subject_code)))
                        ];
                    }
                    $result['data']['time_data'][$i]['required']['teacher'] = $teacher;
                    $result['data']['time_data'][$i]['required']['teacher_id'] = $item->teacher_id;
                    $result['data']['time_data'][$i]['required']['status'] = '';
                    $result['data']['time_data'][$i]['required']['content'] = '';
                    $result['data']['time_data'][$i]['required']['number'] = '';

                    $i++;

                }
            }
            else{

            }
        }
        else{
            //empty events
        }
        return response()->json(['result'=>$result,'status'=>$status]);
    }

//    public function getAvailableUserByEvents(Request $request){
//        $events = $request->get('eventIds');
//        $date = $request->get('date');
//        $dayName = Carbon::parse($request->get('date'));
//
//        $min = Carbon::parse($date)->hour(0)->minute(0)->second(0)->format('Y-m-d\TH:i:s');
//        $max = Carbon::parse($date)->hour(23)->minute(59)->second(59)->format('Y-m-d\TH:i:s');
//
//        if(count($events)>0){
//            $event = $events[0];
//            $eventItem = $this->scheduleRepository->find($event);
//
//            $eventItem = $this->scheduleRepository->find($event);
//            $timeSlotId = $eventItem->date_id;
//            $startDate = $eventItem->start_date;
//
//            $teachers = Teacher::where('id','!=',$eventItem->teacher_id)->get()->toArray();
//            $availableTeachers = $teachers;
//            DB::enableQueryLog();
//            $query = Schedule::where('teacher_id','!=',$eventItem->teacher_id);
////                ->whereDate('start_date',$eventItem->start_date);
//
//            if($dayName->isMonday()){
//                $query->where('day_name','Monday');
//            }elseif($dayName->isTuesday()){
//                $query->where('day_name','Tuesday');
//            }elseif($dayName->isWednesday()){
//                $query->where('day_name','Wednesday');
//            }elseif($dayName->isThursday()){
//                $query->where('day_name','Thursday');
//            }elseif($dayName->isFriday()){
//                $query->where('day_name','Friday');
//            }else{
//                $query->whereNull('day_name');
//            }
//
//            $query->where(function($query) use ($events){
//                foreach($events as $k=> $e){
//                    $et = $this->scheduleRepository->find($e);
//                    if($k==0){
//                        $query->where('slot_id',$et->slot_id);
//                    }
//                    else{
//                        $query->Where('slot_id',$et->slot_id);
//                    }
//
//                }
//            });
//
//            $busyTeachers = $query->groupBy('teacher_id')->orderBy('id','DESC')->get();
////            dd(DB::getQueryLog());
//
//            if(count($busyTeachers) > 0){
//                foreach($busyTeachers as $teacher){
//                    $teacherId = $teacher->teacher_id;
//                    for($i = 0; $i < count($teachers); $i++){
//                        if($teachers[$i]['id'] == $teacherId){
//                            unset($availableTeachers[$i]);
//                        }
//                    }
//                }
//                $availableTeachers = array_values($availableTeachers);
//            }
//
//            $users = [];
//            $timelines = [];
//            $result['data']['time_data'] = [];
////            $result = [
////                'data'=> [
////
////                    'time_data'=>[
////                        [
////                            'required'=>[
////                                'teacher'=>'Teacher name',
////                                'status'=>'unavaliable',
////                                'content'=>'',
////                                'number'=>'',
////                                'classes'=> [
////                                    ['slot'=>[8,9],'lesson'=>'C Phy'],
////                                ]
////                            ]
////                        ]
////                    ]
////
////                ]
////            ];
//            $expand = [];
//            if(count($availableTeachers) > 0){
//                $i=0;
//                dd($availableTeachers);
//                foreach($availableTeachers as $u => $user){
//                    $result['data']['time_data'][$u]['required'] = [
//                        'teacher'=>$user['name'],
//                        'status'=>'unavaliable',
//                        'content'=>'',
//                        'number'=>'',
//                        'classes'=> []
//                    ];
//
//                    $date = Carbon::parse($eventItem->start_date)->format('Y-m-d');
//                    //get timelines foreach user
////                    DB::enableQueryLog();
//                    $query = Schedule::where('teacher_id',$user['id']);
//
//                    $query->where(function($query) use ($events){
//                        foreach($events as $k=> $e){
//                            $et = $this->scheduleRepository->find($e);
//                            if($k==0){
//                                $query->where('slot_id','!=',$et->slot_id);
//                            }
//                            else{
//                                $query->Where('slot_id','!=',$et->slot_id);
//                            }
//
//                        }
//                    });
//
//                    if($dayName->isMonday()){
//                        $query->where('day_name','Monday');
//                    }elseif($dayName->isTuesday()){
//                        $query->where('day_name','Tuesday');
//                    }elseif($dayName->isWednesday()){
//                        $query->where('day_name','Wednesday');
//                    }elseif($dayName->isThursday()){
//                        $query->where('day_name','Thursday');
//                    }elseif($dayName->isFriday()){
//                        $query->where('day_name','Friday');
//                    }else{
//                        $query->whereNull('day_name');
//                    }
//
//                    $userTimelines = $query->get();
////                    ->whereDate('start_date','!=',$eventItem->start_date)->get()->first();
//
//
//                dd(DB::getQueryLog());
////                $item = [
////                    'id'=>$eventItem->id,
////                    'group'=>$user['id'],
////                    'content'=> $eventItem->subject_code,
////                    'start'=> Carbon::parse($eventItem->start_date)->format('Y-m-d\TH:i:s'),
////                    'end'=> Carbon::parse($eventItem->end_date)->format('Y-m-d\TH:i:s'),
////                ];
//
//                    if($userTimelines){
//                        foreach($userTimelines as $k => $uTimeline) {
//                            $result['data']['time_data'][$u]['required']['classes'][] = [
//                                'slot'=>[$uTimeline->slot_id],
//                                'lesson'=>str_replace('/',',',trim(preg_replace('/\r\n|\r|\n/', ',', $uTimeline->subject_code)))
//                            ];
//                        }
//                    }
////                    array_push($result['data']['time_data'],$requiredItem);
//                    $i++;
//                }
//            }
//            else{
//                $result['data']['time_data'] = [
//                    []
//                ];
//            }
//
//
//            return response()->json(['result'=>$result,'status'=>1,'event'=>$eventItem,'min'=>$min,'max'=>$max]);
//        }
//        else{
//            $result['data']['time_data'] = [
//                []
//            ];
//            return response()->json(['result'=>$result,'status'=>0,'event'=>[],'min'=>$min,'max'=>$max]);
//        }
//    }


    public function getUserTimeline(Request $request){
        $teacherId = $request->get('teacher_id');
        $teacher = $this->teacherRepository->find($teacherId);

        $dayName = Carbon::parse($request->get('date'));

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

        $syStartTime = '1:00am';
        $interval = 30;
        $paramDate = $request->get('date');

        $result['data']['time_slot'] = [];

        for($i=1; $i<=17; $i++){
            $startDate = Carbon::createFromFormat('m/d/Y',$paramDate)->setTimeFromTimeString('01:00:00');
            if($i>1){
                $pushMinute = $i*$interval - 30;
                $startDate = $startDate->addMinutes($pushMinute);
            }
            $startTime = substr($startDate->toTimeString(),0,-3);
            $endTime   = substr($startDate->addMinutes($interval)->toTimeString('h:m'),0,-3);

            $timeSlot = [
                'slot' => "$i",
                'start'=>$startTime,
                'end'=>$endTime
            ];
            array_push($result['data']['time_slot'],$timeSlot);
        }
//        $result['data']['time_data'] = [
//            [
//                'required'=> [
//                    'teacher'=> $teacher->name,
//                    'classes' => [
//                        [
//                            'class' => ['ABC','EDF'],
//                            'lesson'=> 'SC',
//                            'slot'=> [9,10],
//                            'start'=> '08:00',
//                            'end'=>'09:00',
//                            'status'=>'unavaliable',
//                            'content'=>'relif made',
//                            'number'=> '99'
//                        ],
//                        [
//                            'class' => ['EF','DSZ'],
//                            'lesson'=> 'SAC',
//                            'slot'=> [1,2,3,4],
//                            'start'=> '08:00',
//                            'end'=>'09:00',
//                            'status'=>'unavaliable',
//                            'content'=>'relif made',
//                            'number'=> '99'
//                        ]
//                    ],
//                ],
//                'paired'=>
//                    [
//                        [
//                            'class'=> '4N2',
//                            'lesson'=> '4N2',
//                            'slot'=> [11,12],
//                            'start'=> '8:00',
//                            'end'=> '10:00',
//                        ],
//                        [
//                            'class'=> 'AVD',
//                            'lesson'=> 'SW',
//                            'slot'=> [13,14],
//                            'start'=> '8:00',
//                            'end'=> '10:00',
//                        ]
//                    ]
//            ],
//
//        ];
        $result['data']['time_data'][0] = [];
        $result['data']['time_data'][0]['required']['teacher'] = $teacher->name;
        $classes = $result['data']['time_data'][0]['required']['classes'] = [];

        $result['data']['time_data'][0]['paired'] =[];

        if($rows){
            foreach($rows as $row){
                $data = [
                    'id'=>$row->id,
                    'class' => '',
                    'lesson'=> str_replace('\n','/',$row->subject_code),
                    'slot'=> [$row->slot_id],
                    'start'=> substr($row->start_time,0,-3),
                    'end'=> substr($row->end_time,0,-3),
                    'status'=>'unavaliable',
                    'content'=>'relif made',
                    'number'=> '99'
                ];

//                $data = [
//                    'id'=>$row->id,
//                    'group'=>$teacherId,
//                    'content'=> $row->subject_code,
//                    'start'=>Carbon::parse($paramDate)->setTimeFromTimeString($row->start_time)->format('Y-m-d\TH:i:s'),
//                    'end'=>Carbon::parse($paramDate)->setTimeFromTimeString($row->end_time)->format('Y-m-d\TH:i:s'),
//                ];
                array_push($classes,$data);
            }
            $result['data']['time_data'][0]['required']['classes'] = $classes;
            return response()->json(['result'=>$result,'status'=>1]);
        }else{
            return response()->json(['result'=>$result,'status'=>0]);
        }

    }
    public function getAvailableUser(Request $request){
        $event = $request->get('eventId');

        $eventItem = $this->scheduleRepository->find($event);
        $timeSlotId = $eventItem->date_id;
        $startDate = $eventItem->start_date;

//        $availableUser = Teacher::whereHas('schedule', function($q) use ($startDate){
//            $q->whereDate('start_date','!=',$startDate);
//        })->get()->toArray();


        $teachers = Teacher::where('id','!=',$eventItem->teacher_id)->get()->toArray();
        $availableTeachers = $teachers;
        $busyTeachers = Schedule::where('teacher_id','!=',$eventItem->teacher_id)
//            ->whereBetween('start_date',[$eventItem->start_date,$eventItem->end_date])
            ->whereDate('start_date',$eventItem->start_date)
            ->where('date_id',$timeSlotId)
            ->groupBy('teacher_id')->get();

        if(count($busyTeachers) > 0){
            foreach($busyTeachers as $teacher){
                $teacherId = $teacher->teacher_id;
                for($i = 0; $i < count($teachers); $i++){
                    if($teachers[$i]['id'] == $teacherId){
                        unset($availableTeachers[$i]);
                    }
                }
            }
            $availableTeachers = array_values($availableTeachers);
        }



        $users = [];
        $timelines = [];
        $expand = [];
        if(count($availableTeachers) > 0){
            $i=0;
            foreach($availableTeachers as $user){
                $row = [
                    'id'=>$user['id'],
                    'content'=>$user['name'],
                    'value'=>$user['id'],
                ];
                array_push($users,$row);
                $i++;
            }
        }

        if(count($users)>0){
            $date = Carbon::parse($eventItem->start_date)->format('Y-m-d');
            foreach ($users as $key=> $user){
                //get timelines foreach user
                DB::enableQueryLog();
                $userTimelines = Schedule::where('teacher_id',$user['id'])
                    ->where('date_id','>',$timeSlotId)
                    ->whereDate('start_date',$date)->get();
//                    ->whereDate('start_date','!=',$eventItem->start_date)->get()->first();


//                dd(DB::getQueryLog());
//                $item = [
//                    'id'=>$eventItem->id,
//                    'group'=>$user['id'],
//                    'content'=> $eventItem->subject_code,
//                    'start'=> Carbon::parse($eventItem->start_date)->format('Y-m-d\TH:i:s'),
//                    'end'=> Carbon::parse($eventItem->end_date)->format('Y-m-d\TH:i:s'),
//                ];

                if($userTimelines){
                    foreach($userTimelines as $k => $uTimeline) {

                        if($k==0){
                            $item = [
                                'id' => $eventItem->id.'_'.uniqid(),
                                'group' => $user['id'],
                                'content' => '',
                                'start' => Carbon::parse($eventItem->start_date)->format('Y-m-d\TH:i:s'),
                                'end' => Carbon::parse($eventItem->end_date)->format('Y-m-d\TH:i:s'),
                                'className'=> 'orange',
                            ];
                            array_push($timelines, $item);
                        }
                        $item = [
                            'id' => $uTimeline->id,
                            'group' => $user['id'],
                            'content' => $uTimeline->subject_code,
                            'start' => Carbon::parse($uTimeline->start_date)->format('Y-m-d\TH:i:s'),
                            'end' => Carbon::parse($uTimeline->end_date)->format('Y-m-d\TH:i:s'),
                        ];

                        array_push($timelines, $item);
                    }
                }
            }
        }


        $min = Carbon::parse($eventItem->start_date)->hour(0)->minute(0)->second(0)->format('Y-m-d\TH:i:s');
        $max = Carbon::parse($eventItem->end_date)->hour(23)->minute(59)->second(59)->format('Y-m-d\TH:i:s');

        $result = [
            'users' => $users,
            'timelines'=>$timelines
        ];
        return response()->json(['result'=>$result,'status'=>1,'event'=>$eventItem,'min'=>$min,'max'=>$max]);
    }


    public function getAssignFormModal(Request $request){

        $teacher_id = $request->get('teacher_id');
        $teacher = $this->teacherRepository->find($teacher_id)->first();
        $scheduleIds = $request->get('schedule_ids');
        $selectedDate = Carbon::parse($request->get('selected_date'))->toDateString();

        if(count($scheduleIds) > 0){
            $schedules = Schedule::whereIn('id',$scheduleIds)->get();
        }

        return view('schedule::admin.schedule.assign_form_modal',compact('teacher','schedules','selectedDate'));
    }
}

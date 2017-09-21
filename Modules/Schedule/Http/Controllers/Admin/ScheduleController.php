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
use Modules\Schedule\Entities\Teacher;
use Modules\Schedule\Events\ImportExcelSchedule;
use Modules\Schedule\Http\Requests\UploadExcelRequest;
use Modules\Schedule\Repositories\ScheduleRepository;
use Modules\Schedule\Repositories\TeacherRepository;
use Modules\User\Contracts\Authentication;
use Modules\User\Permissions\PermissionManager;
use Modules\User\Repositories\RoleRepository;
use Modules\User\Repositories\UserRepository;

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

        return view('schedule::admin.schedule.index', compact('currentUser','teachers'));
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
        $limitRow = 10;

        $limitRunRow = $highestRow / $limitRow;

        $daysInWeek = ['Monday','Tuesday','Wednesday','Thursday','Friday'];
        $hoursInDays = 17;
        //update schedule
        $week = $this->_get_current_week();
        //
        $mergeCells = $objWorksheet->getMergeCells();

        DB::table('makeit__schedules')->delete();
        DB::table('makeit__teachers')->delete();

        for ($row = 1; $row <= $limitRunRow; $row++) {

            Log::info('=====start processing row ' . $row . '=========');
            event(new ImportExcelSchedule($path, $limitRow, $row,$interval,$startTime));
            Log::info('=====end processing row ' . $row . '=========');
        }

        $request->session()->flash('success','Upload excel file successfully');
        return redirect()->back();

//        Storage::disk('local')->put($request->file('file'), 'public');
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
    public function import(Request $request)
    {
        if($request->file('imported-file'))
        {
//            $path = $request->file('imported-file')->getRealPath();
            $path = public_path()."/uploads/Teachers_Timetable.xlsx";

            $objPHPExcel = \PHPExcel_IOFactory::load($path);
            $objWorksheet = $objPHPExcel->getActiveSheet();
            $highestRow = $objWorksheet->getHighestRow();
            $limitRow = 10;

            $limitRunRow = $highestRow / $limitRow;


            $daysInWeek = ['Monday','Tuesday','Wednesday','Thursday','Friday'];
            $hoursInDays = 17;

            //update schedule
            $week = $this->_get_current_week();


            //
            $mergeCells = $objWorksheet->getMergeCells();

            $result = [];

            for ($row = 1; $row <= $limitRunRow; $row++) {

                Log::info('=====start processing row ' . $row.'=========');
                event(new ImportExcelSchedule($path,$limitRow,$row));
                Log::info('=====end processing row ' . $row.'=========');

//                $teacher = $objWorksheet->getCellByColumnAndRow(0 , $row)->getValue();
//                $scheduleRow = [];
//                if(!empty($teacher)){
//                    $n=0;
//                    for($j=1; $j<=$hoursInDays*count($daysInWeek);$j++){
////                        $column = $objWorksheet->getCellByColumnAndRow($j,$row)->getColumn();
//                        $cellNum = $objWorksheet->getCellByColumnAndRow($j,$row)->getRow();
////                        $cell = $objWorksheet->getCell($column.$cellNum);
//
//                        if (!$objWorksheet->getCellByColumnAndRow( $j,$row)->isInMergeRange() || $objWorksheet->getCellByColumnAndRow( $j,$row )->isMergeRangeValueCell()) {
//                            $scheduleRow[$daysInWeek[$n]][$j] = $objWorksheet->getCellByColumnAndRow($j , $row)->getValue();
//                        } else {
//                            for($k=0; $k<=100 ; $k++){
//                                if($objWorksheet->getCellByColumnAndRow($j-$k , $row)->getValue() != null){
//                                    $scheduleRow[$daysInWeek[$n]][$j] = $objWorksheet->getCellByColumnAndRow($j-$k , $row)->getValue();
//                                    break;
//                                }
//                            }
//
//                        }
//                        if($j%$hoursInDays==0){
//                            $n++;
//                        }
//                    }
//                    $result[] = [
//                        'name'=>$teacher,
//                        'schedule' => $scheduleRow
//                    ];
//
//                }
//                else{
//                    //teacher name blank
//                }
//
            }
//            dd($result);

        }
        return back();
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
        $date = Carbon::createFromFormat('m/d/Y',$date)->format('Y-m-d');

        DB::enableQueryLog();
//        $rows = $this->scheduleRepository->getByAttributes(['start_date'=>$date]);
        $rows = Schedule::with('teacher')->whereDate('start_date',$date)->groupBy('teacher_id')->get();
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

    public function getUserTimeline(Request $request){
        $teacherId = $request->get('teacher_id');
        $startDate = Carbon::createFromFormat('m/d/Y',$request->get('date'))->format('Y-m-d');


        $min = Carbon::createFromFormat('m/d/Y',$request->get('date'))->hour(0)->minute(0)->second(0)->format('Y-m-d\TH:i:s');
        $max = Carbon::createFromFormat('m/d/Y',$request->get('date'))->hour(23)->minute(59)->second(59)->format('Y-m-d\TH:i:s');

//        $rows = $this->scheduleRepository->getByAttributes(['teacher_id'=>$teacherId,'start_date'=>$startDate]);
        $rows = Schedule::where('teacher_id',$teacherId)->whereDate('start_date',$startDate)->get();

        $result = [];
        if($rows){
            foreach($rows as $row){
                $data = [
                    'id'=>$row->id,
                    'content'=> $row->subject_code,
                    'start'=>Carbon::parse($row->start_date)->format('Y-m-d\TH:i:s'),
                    'end'=>Carbon::parse($row->end_date)->format('Y-m-d\TH:i:s'),
                ];
                array_push($result,$data);
            }
            return response()->json(['result'=>$result,'status'=>1,'min'=>$min,'max'=>$max]);
        }else{
            return response()->json(['result'=>$result,'status'=>0,'min'=>$min,'max'=>$max]);
        }

    }
    public function getAvailableUser(Request $request){
        $event = $request->get('eventId');

        $eventItem = $this->scheduleRepository->find($event);
        $startDate = $eventItem->start_date;

//        $availableUser = Teacher::whereHas('schedule', function($q) use ($startDate){
//            $q->whereDate('start_date','!=',$startDate);
//        })->get()->toArray();


        $teachers = Teacher::where('id','!=',$eventItem->teacher_id)->get()->toArray();
        $availableTeachers = $teachers;
        $busyTeachers = Schedule::where('teacher_id','!=',$eventItem->teacher_id)
            ->whereBetween('start_date',[$eventItem->start_date,$eventItem->end_date])
//            ->whereDate('end_date','<=',$eventItem->end_date)
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
                    ->whereDate('start_date',$date)
                    ->whereDate('start_date','!=',$eventItem->start_date)->get()->first();


//                dd(DB::getQueryLog());
                $item = [
                    'id'=>$eventItem->id,
                    'group'=>$user['id'],
                    'content'=> $eventItem->subject_code,
                    'start'=> Carbon::parse($eventItem->start_date)->format('Y-m-d\TH:i:s'),
                    'end'=> Carbon::parse($eventItem->end_date)->format('Y-m-d\TH:i:s'),
                ];

                if($userTimelines){
//                    foreach($userTimelines as $k => $uTimeline){
                        $item = [
                            'id'=>$userTimelines->id,
                            'group'=>$user['id'],
                            'content'=> urlencode($userTimelines->subject_code),
                            'start'=> Carbon::parse($userTimelines->start_date)->format('Y-m-d\TH:i:s'),
                            'end'=> Carbon::parse($userTimelines->end_date)->format('Y-m-d\TH:i:s'),
                        ];

                    array_push($timelines,$item);
                }
            }
            if(count($expand) > 0){
                foreach($expand as $item){
                    $no = count($timelines)+1;
                    $item['id']= $no;
//                    array_push($timelines,$item);
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

    public function sendNotification(Request $request){

        $request->session()->flash('success','Send SMS successfully');
//        dd($request->all());
        return redirect()->back();
    }

    public function getUserByEvent(Request $request){
        $event = $this->scheduleRepository->find($request->get('eventId'));
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
}

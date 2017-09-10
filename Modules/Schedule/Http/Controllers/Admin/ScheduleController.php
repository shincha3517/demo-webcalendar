<?php

namespace Modules\Schedule\Http\Controllers\Admin;

use Carbon\Carbon;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Core\Http\Controllers\Admin\AdminBaseController;
use Modules\Schedule\Events\ImportExcelSchedule;
use Modules\Schedule\Http\Requests\UploadExcelRequest;
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

    /**
     * @param PermissionManager $permissions
     * @param UserRepository    $user
     * @param RoleRepository    $role
     * @param Authentication    $auth
     */
    public function __construct(
        Authentication $auth,
        TeacherRepository $teacherRepository
    ) {
        parent::__construct();

        $this->auth = $auth;
        $this->teacherRepository = $teacherRepository;
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

    public function doUpload(UploadExcelRequest $request){
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
        for ($row = 1; $row <= $limitRunRow; $row++) {

            Log::info('=====start processing row ' . $row . '=========');
            event(new ImportExcelSchedule($path, $limitRow, $row));
            Log::info('=====end processing row ' . $row . '=========');
        }

        return redirect()->back();

//        Storage::disk('local')->put($request->file('file'), 'public');
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

        return $w;
    }
}

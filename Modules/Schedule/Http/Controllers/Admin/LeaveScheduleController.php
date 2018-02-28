<?php
/**
 * Created by PhpStorm.
 * User: eric
 * Date: 1/26/18
 * Time: 9:28 AM
 */

namespace Modules\Schedule\Http\Controllers\Admin;


use Carbon\Carbon;
use Modules\Core\Http\Controllers\Admin\AdminBaseController;
use Modules\Schedule\Entities\Assignment;
use Modules\Schedule\Repositories\AssignmentRepository;
use Modules\Schedule\Repositories\EventScheduleRepository;
use Modules\Schedule\Repositories\ScheduleRepository;
use Modules\Schedule\Repositories\TeacherRepository;
use Modules\Setting\Entities\Setting;
use Modules\User\Contracts\Authentication;
use Illuminate\Http\Request;

class LeaveScheduleController extends AdminBaseController
{
    /**
     * @var Authentication
     */
    private $auth;

    protected $teacherRepository;
    protected $scheduleRepository;
    protected $eventScheduleRepository;
    protected $assignmentRepository;
    protected $repository;
    protected $setting;

    /**
     * @param PermissionManager $permissions
     * @param UserRepository    $user
     * @param RoleRepository    $role
     * @param Authentication    $auth
     */
    public function __construct(
        Authentication $auth,
        TeacherRepository $teacherRepository,
        ScheduleRepository $scheduleRepository,
        EventScheduleRepository $eventScheduleRepository,
        AssignmentRepository $assignmentRepository,
        Setting $setting
    ){
        parent::__construct();

        $this->auth = $auth;
        $this->teacherRepository = $teacherRepository;
        $this->scheduleRepository = $scheduleRepository;
        $this->eventScheduleRepository = $eventScheduleRepository;
        $this->assignmentRepository = $assignmentRepository;
        $this->setting = $setting;
    }

    public function index(Request $request){
        $currentUser = $this->auth->user();
        if($currentUser->id == 1){
            return view('schedule::admin.schedule.non_available');
        }
        $teacher = $this->teacherRepository->findByAttributes(['email'=>$currentUser->email]);
        $teachers = $this->teacherRepository->all();

        $leaves = [];
        if($teacher){
            $assignmentList = $this->assignmentRepository->getByAttributes(['is_past'=>0,'teacher_id'=>$teacher->id,'slot_id'=> null]);
        }else{
            $assignmentList = $this->assignmentRepository->getByAttributes(['is_past'=>0,'slot_id'=> null]);
        }

        if($assignmentList){
            $collection = collect($assignmentList);
            $assignmentArray = $collection->map(function ($item, $key) {
                return Carbon::parse($item->selected_date)->format('j/n/Y');
            });
            $leaves = $assignmentArray->all();
        }


        $date = '10/18/2017';
        $timeSlot = $this->scheduleRepository->getOldTimeSlot($date);

        return view('schedule::admin.schedule.worker', compact('currentUser','teachers','teacher','timeSlot','leaves'));
    }

    private function _getRepository($scheduleTable){
        if($scheduleTable == 'old'){
            $this->repository = $this->scheduleRepository;
        }else{
            $this->repository = $this->eventScheduleRepository;
        }
    }
    private function _getScheduleTable($selectedDate){
        if(!$selectedDate or empty($selectedDate) or !Carbon::parse($selectedDate) ){
            return false;
        }
        $selectedDate = Carbon::parse($selectedDate);

        $weekOfMonth = $selectedDate->weekOfMonth;
        $weekOfYear = $selectedDate->weekOfYear;

        $oddWeek = [1,3,5,7,9,12,14,16,18,20,26,28,31,33,35,37,39,41,43,45];
        $evenWeek = [2,4,6,8,10,13,15,17,19,21,27,29,30,32,34,38,40,42,44,46];

        if(in_array($weekOfYear,$oddWeek)){
            $tableName = 'old';
        }elseif(in_array($weekOfYear,$evenWeek)){
            $tableName = 'event';
        }else{
            $tableName = '';
        }
        return $tableName;
    }

    public function getLeavesByDate(Request $request){
        $date = $request->get('date');
        $date = Carbon::createFromFormat('m/d/Y',$date)->toDateString();
        $dayName = Carbon::parse($date);

        $currentUser = $this->auth->user();
        $teacher = $this->teacherRepository->findByAttributes(['email'=>$currentUser->email]);

        $scheduleTable = $this->_getScheduleTable($date);
        $this->_getRepository($scheduleTable);

        $leaves = Assignment::where(['selected_date'=> $date,'teacher_id'=>$teacher->id,'is_past'=>0])->groupBy('code')->get();
        if(count($leaves) > 0){
            $collection = collect($leaves);
            $leavesArray = $collection->map(function ($item, $key) {
                return [
                    'id'=>$item->id,
                    'teacher_name'=>$item->teacher_name,
                    'replaced_teacher_name'=>$item->replaced_teacher_name,
                    'lesson'=>$item->lesson,
                    'start_time'=>substr(Carbon::parse($item->start_date)->toTimeString(),0,-3),
                    'end_time'=>substr(Carbon::parse($item->end_date)->toTimeString(),0,-3),
                    'schedule_type'=>$item->schedule_type,
                    'start_date'=> Carbon::parse($item->start_date)->toDateTimeString(),
                    'end_date'=> Carbon::parse($item->end_date)->toDateTimeString(),
                    'status'=> $item->status
                ];
            });
            $leaveItems = $leavesArray->all();
            return response()->json(['result'=>$leaveItems,'status'=>1,'schedule'=>$scheduleTable]);
        }else{
            return response()->json(['result'=>[],'status'=>0,'schedule'=>$scheduleTable]);
        }
    }

    public function cancelLeaveItem(Request $request){
        if($request->has('leave_id')){
            $leaveId = $request->get('leave_id');

            $item = $this->assignmentRepository->find($leaveId);
            if($item){
                $this->assignmentRepository->destroy($item);

                Assignment::where('code',$item->code)->delete();


                return response()->json([
                   'status'=>1,
                   'leave_id'=> $leaveId,
                   'msg'=> 'cancel leave item successful'
                ]);
            }
        }
        return response()->json([
            'status'=> 0,
            'msg'=> 'cancel leave item failure'
        ]);
    }
}
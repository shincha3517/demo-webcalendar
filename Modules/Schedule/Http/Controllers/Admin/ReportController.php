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
use Modules\Schedule\Entities\Activity;
use Modules\Schedule\Entities\Schedule;
use Modules\Schedule\Entities\ScheduleDate;
use Modules\Schedule\Entities\Teacher;
use Modules\Schedule\Events\Handlers\InsertTeacherExcelSchedule;
use Modules\Schedule\Events\ImportExcelSchedule;
use Modules\Schedule\Events\ReadEventSchedule;
use Modules\Schedule\Events\ReadTeacherExcelFile;
use Modules\Schedule\Http\Requests\UploadExcelRequest;
use Modules\Schedule\Repositories\ScheduleRepository;
use Modules\Schedule\Repositories\TeacherRepository;
use Modules\User\Contracts\Authentication;
use Modules\User\Permissions\PermissionManager;
use Modules\User\Repositories\RoleRepository;
use Modules\User\Repositories\UserRepository;
use Nexmo\Laravel\Facade\Nexmo;

class ReportController extends AdminBaseController
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

        return view('schedule::admin.schedule.report.index');
    }


}

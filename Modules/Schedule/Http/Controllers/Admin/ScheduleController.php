<?php

namespace Modules\Schedule\Http\Controllers\Admin;

use Illuminate\Http\Response;
use Modules\Core\Http\Controllers\Admin\AdminBaseController;
use Modules\User\Contracts\Authentication;
use Modules\User\Permissions\PermissionManager;
use Modules\User\Repositories\RoleRepository;
use Modules\User\Repositories\UserRepository;

class ScheduleController extends AdminBaseController
{
    /**
     * @var Authentication
     */
    private $auth;

    /**
     * @param PermissionManager $permissions
     * @param UserRepository    $user
     * @param RoleRepository    $role
     * @param Authentication    $auth
     */
    public function __construct(
        Authentication $auth
    ) {
        parent::__construct();

        $this->auth = $auth;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $currentUser = $this->auth->user();

        return view('schedule::admin.schedule.index', compact('currentUser'));
    }

    public function getUpload(){
        return view('schedule::admin.schedule.upload', compact(''));
    }

    public function doUpload(){

    }
}

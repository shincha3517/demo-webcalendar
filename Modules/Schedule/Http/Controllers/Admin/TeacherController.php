<?php

namespace Modules\Schedule\Http\Controllers\Admin;

use Modules\Core\Http\Controllers\Admin\AdminBaseController;
use Modules\Schedule\Entities\Teacher;
use Modules\Schedule\Http\Requests\Teacher\CreateTeacherRequest;
use Modules\Schedule\Http\Requests\Teacher\UpdateTeacherRequest;
use Modules\Schedule\Repositories\TeacherRepository;
use Modules\User\Entities\Sentinel\User;
use Modules\User\Repositories\RoleRepository;
use Modules\User\Repositories\UserRepository;

class TeacherController extends AdminBaseController
{
    /**
     * @var TeacherRepository
     */
    private $teacher;
    private $user;
    private $role;

    public function __construct(TeacherRepository $teacher, UserRepository $user , RoleRepository $role)
    {
        parent::__construct();

        $this->teacher = $teacher;
        $this->user = $user;
        $this->role = $role;
    }

    public function index()
    {
        $teachers = Teacher::with('user')->get();

        return view('schedule::admin.teacher.index', compact('teachers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $roles = $this->role->all();
        return view('schedule::admin.teacher.create',compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CreatePageRequest $request
     * @return Response
     */
    public function store( CreateTeacherRequest $request)
    {
        $data = [
            'first_name' => $request->name,
            'last_name'=> 'a',
            "email" => $request->email,
            "activated" => "1",
            "roles" => $request->get('roles'),
            "permissions" => [],
            "password" => $request->get('password'),
            "password_confirmation" => $request->get('password')
        ];

        $user = $this->user->createWithRoles($data, $request->get('roles'), true);

        $teacherData = [
            'name'=> $request->get('name'),
            'phone_number'=> $request->get('phone_number'),
            'email'=> $request->get('email'),
            'subject'=> $request->get('subject'),
            'user_id'=> $user->id,
            'teacher_type' => $request->get('teacher_type') ? 1:0,
            'is_leave_notify'=> in_array(4,$request->get('roles')) ? 1 : 0
        ];

        $this->teacher->create($teacherData);

        return redirect()->route('admin.schedule.teacher.index')
            ->withSuccess('Teacher successfully created.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Page $page
     * @return Response
     */
    public function edit($id)
    {
        $teacher = $this->teacher->find($id);
        $roles = $this->role->all();

        if(!$teacher){
            return redirect()->route('admin.schedule.teacher.index')
                ->withErrors('Teacher does not exist','error');
        }

        return view('schedule::admin.teacher.edit', compact('teacher','roles'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Page $page
     * @param  UpdatePageRequest $request
     * @return Response
     */
    public function update($id, UpdateTeacherRequest $request)
    {
        $teacher = $this->teacher->find($id);

        if(in_array(4,$request->get('roles'))){
            $request->request->add(['is_leave_notify' => 1]);
        }else{
            $request->request->add(['is_leave_notify' => 0]);
        }

        $this->teacher->update($teacher, $request->all());

        $user = $teacher->user;
        if($user){
            $data = [
                'first_name' => $request->name,
                'last_name'=> 'a',
                "email" => $request->email,
                "activated" => "1",
                "roles" => $request->get('roles'),
                "permissions" => [],
                "password" => $request->get('password'),
                "password_confirmation" => $request->get('password')
            ];
            $this->user->updateAndSyncRoles($user->id,$data, $request->get('roles'));
        }

        if ($request->get('button') === 'index') {
            return redirect()->route('admin.schedule.teacher.index')
                ->withSuccess('Teacher successfully updated.');
        }

        return redirect()->back()
            ->withSuccess('Teacher successfully updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Page $page
     * @return Response
     */
    public function destroy(Teacher $teacher)
    {
        if($teacher->user_id){
            //destroy user
            $user = $this->user->find($teacher->user_id);
        }
        //destroy teacher
        $this->teacher->destroy($teacher);

        return redirect()->route('admin.schedule.teacher.index')
            ->withSuccess(trans('page::messages.page deleted'));
    }
}

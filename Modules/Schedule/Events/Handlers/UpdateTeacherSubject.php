<?php
/**
 * Created by PhpStorm.
 * User: eric
 * Date: 8/25/17
 * Time: 11:39
 */
namespace Modules\Schedule\Events\Handlers;

use Carbon\Carbon;
use DebugBar\DebugBar;
use Doctrine\DBAL\Query\QueryException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Modules\Page\Events\PageIsCreating;
use Modules\Schedule\Entities\ScheduleDate;
use Modules\Schedule\Events\ImportExcelSchedule;
use Modules\Schedule\Events\ReadEventSchedule;
use Modules\Schedule\Events\ReadSubjectSheet;
use Modules\Schedule\Events\ReadTeacherExcelFile;
use Modules\Schedule\Repositories\EventScheduleRepository;
use Modules\Schedule\Repositories\ScheduleRepository;
use Modules\Schedule\Repositories\TeacherRepository;
use Modules\User\Repositories\UserRepository;

class UpdateTeacherSubject implements ShouldQueue
{
    public $tries = 1;
    public $timeout = 360;
    public $user;

    use InteractsWithQueue;
    /**
     * Create the event listener.
     *
     * @return void
     */
    protected $teacherRepository;

    public function __construct(TeacherRepository $teacherRepository, UserRepository $user)
    {
        $this->teacherRepository = $teacherRepository;
        $this->user = $user;
    }

    /**
     * Handle the event.
     *
     * @param  ImportExcelSchedule  $event
     * @return void
     */
    public function handle(ReadSubjectSheet $event)
    {
//        if (true) {
//            $this->release(2);
//        }
        $rowNumber = $event->rowNumber;

        $path = storage_path('imports')."/import.xlsx";
        $objPHPExcel = \PHPExcel_IOFactory::load($path);
        $objWorksheet = $objPHPExcel->getSheet(2);

        $teacherName = $objWorksheet->getCellByColumnAndRow(0 , $rowNumber)->getValue();

        $teacher = $this->teacherRepository->findByAttributes(['name'=>$teacherName]);
        if($teacher){
            Log::info('====================================== START UPDATE TEACHER' . $teacherName.'==========================');
            $subject = $objWorksheet->getCellByColumnAndRow(1 , $rowNumber)->getValue();
            $phone = $objWorksheet->getCellByColumnAndRow(2 , $rowNumber)->getValue();
            $email = $objWorksheet->getCellByColumnAndRow(3 , $rowNumber)->getValue();
            $isAdmin = $objWorksheet->getCellByColumnAndRow(4 , $rowNumber)->getValue();
            if(!empty($subject)){
                $teacher->subject = $subject;
                $teacher->phone_number = $phone;
                $teacher->email = $email;
                $teacher->save();
            }

            if(strtolower($isAdmin) == 'yes'){
                $data = [
                    'first_name'=> $teacherName,
                    'last_name'=> '',
                    'email'=> $email,
                    'password'=> 'Yh3019',
                    'password_confirmation'=> 'Yh3019',
                    'roles'=>['1'],
                    'permissions'=>[
                        "core.sidebar.group" => true,
                        "dashboard.index" => true,
                        "dashboard.update" => true,
                        "dashboard.reset" => true,
                        "media.medias.index" => false,
                        "media.medias.create" => false,
                        "media.medias.edit" => false,
                        "media.medias.destroy" => false,
                        "menu.menus.index" => false,
                        "menu.menus.create" => false,
                        "menu.menus.edit" => false,
                        "menu.menus.destroy" => false,
                        "menu.menuitems.index" => false,
                        "menu.menuitems.create" => false,
                        "menu.menuitems.edit" => false,
                        "menu.menuitems.destroy" => false,
                        "page.pages.index" => false,
                        "page.pages.create" => false,
                        "page.pages.edit" => false,
                        "page.pages.destroy" => false,
                        "schedule.schedules.upload" => false,
                        "schedule.schedules.index" => true,
                        "schedule.schedules.worker" => false,
                        "schedule.report.index" => true,
                        "schedule.report.list" => true,
                        "schedule.report.export" => true,
                        "schedule.meeting.index" => false,
                        "schedule.meeting.create" => false,
                        "schedule.meeting.edit" => false,
                        "setting.settings.index" => false,
                        "setting.settings.edit" => false,
                        "tag.tags.index" => false,
                        "tag.tags.create" => false,
                        "tag.tags.edit" => false,
                        "tag.tags.destroy" => false,
                        "translation.translations.index" => false,
                        "translation.translations.edit" => false,
                        "translation.translations.import" => false,
                        "translation.translations.export" => false,
                        "user.users.index" => false,
                        "user.users.create" => false,
                        "user.users.edit" => false,
                        "user.users.destroy" => false,
                        "user.roles.index" => false,
                        "user.roles.create" => false,
                        "user.roles.edit" => false,
                        "user.roles.destroy" => false,
                        "workshop.sidebar.group" => false,
                        "workshop.modules.index" => false,
                        "workshop.modules.show" => false,
                        "workshop.modules.update" => false,
                        "workshop.modules.disable" => false,
                        "workshop.modules.enable" => false,
                        "workshop.modules.publish" => false,
                        "workshop.themes.index" => false,
                        "workshop.themes.show" => false,
                        "workshop.themes.publish" => false,
                    ]
                ];

                $checkTeacher = $this->user->findByCredentials(['email'=> $email]);
                if(!$checkTeacher){
                    try{
                        $this->user->createWithRoles($data, ['1'], true);
                    }catch (QueryException $e){
                        Log::error('Can not create teacher '.$teacherName .' because'. $e->getMessage());
                    }
                }else{
                    Log::error('Teacher with email:'.$email .' already exist');
                }



            }elseif(strtolower($isAdmin) == 'no') {
                $data = [
                    'first_name'=> $teacherName,
                    'last_name'=> '',
                    'email'=> $email,
                    'password'=> '123456',
                    'password_confirmation'=> '123456',
                    'roles'=>['2'],
                    'permissions'=>[
                        "core.sidebar.group" => true,
                        "dashboard.index" => true,
                        "dashboard.update" => true,
                        "dashboard.reset" => true,
                        "media.medias.index" => false,
                        "media.medias.create" => false,
                        "media.medias.edit" => false,
                        "media.medias.destroy" => false,
                        "menu.menus.index" => false,
                        "menu.menus.create" => false,
                        "menu.menus.edit" => false,
                        "menu.menus.destroy" => false,
                        "menu.menuitems.index" => false,
                        "menu.menuitems.create" => false,
                        "menu.menuitems.edit" => false,
                        "menu.menuitems.destroy" => false,
                        "page.pages.index" => false,
                        "page.pages.create" => false,
                        "page.pages.edit" => false,
                        "page.pages.destroy" => false,
                        "schedule.schedules.upload" => false,
                        "schedule.schedules.index" => false,
                        "schedule.schedules.worker" => true,
                        "schedule.report.index" => false,
                        "schedule.report.list" => false,
                        "schedule.report.export" => false,
                        "schedule.meeting.index" => false,
                        "schedule.meeting.create" => false,
                        "schedule.meeting.edit" => false,
                        "setting.settings.index" => false,
                        "setting.settings.edit" => false,
                        "tag.tags.index" => false,
                        "tag.tags.create" => false,
                        "tag.tags.edit" => false,
                        "tag.tags.destroy" => false,
                        "translation.translations.index" => false,
                        "translation.translations.edit" => false,
                        "translation.translations.import" => false,
                        "translation.translations.export" => false,
                        "user.users.index" => false,
                        "user.users.create" => false,
                        "user.users.edit" => false,
                        "user.users.destroy" => false,
                        "user.roles.index" => false,
                        "user.roles.create" => false,
                        "user.roles.edit" => false,
                        "user.roles.destroy" => false,
                        "workshop.sidebar.group" => false,
                        "workshop.modules.index" => false,
                        "workshop.modules.show" => false,
                        "workshop.modules.update" => false,
                        "workshop.modules.disable" => false,
                        "workshop.modules.enable" => false,
                        "workshop.modules.publish" => false,
                        "workshop.themes.index" => false,
                        "workshop.themes.show" => false,
                        "workshop.themes.publish" => false,
                    ]
                ];

                $checkTeacher = $this->user->findByCredentials(['email'=> $email]);
                if(!$checkTeacher){
                    try{
                        $this->user->createWithRoles($data, ['2'], true);
                    }catch (QueryException $e){
                        Log::error('Can not create teacher '.$teacherName .' because'. $e->getMessage());
                    }
                }else{
                    Log::error('Teacher with email:'.$email .' already exist');
                }
            }

            Log::info('====================================== END UPDATE TEACHER' . $teacherName.'==========================');
        }
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


        $scheduleItem = ScheduleDate::get()->first();
        $oldTotalTimeSlot = $scheduleItem->old_total_timeslots;
        $result = '';
        if($rowNo >=1 && $rowNo <=$oldTotalTimeSlot){
            $result = Carbon::createFromFormat('Y-m-d g:ia',$monday.' '.$startTime);
        }
        if($rowNo >$oldTotalTimeSlot && $rowNo <=$oldTotalTimeSlot*2){
            $result = Carbon::createFromFormat('Y-m-d g:ia',$tuesday.' '.$startTime);
        }
        if($rowNo > $oldTotalTimeSlot*2 && $rowNo <=$oldTotalTimeSlot*3){
            $result = Carbon::createFromFormat('Y-m-d g:ia',$wednesday.' '.$startTime);
        }
        if($rowNo >$oldTotalTimeSlot*3  && $rowNo <=$oldTotalTimeSlot*4){
            $result = Carbon::createFromFormat('Y-m-d g:ia',$thursday.' '.$startTime);
        }
        if($rowNo >$oldTotalTimeSlot*4 && $rowNo <=$oldTotalTimeSlot*5){
            $result = Carbon::createFromFormat('Y-m-d g:ia',$friday.' '.$startTime);
        }
        if($resetRowNo > 1 ){
            $result = $result->addMinutes( ($interval*$resetRowNo)-$interval );
        }
        return $result;
    }
}
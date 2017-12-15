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

class UpdateTeacherSubject implements ShouldQueue
{
    public $tries = 1;
    public $timeout = 360;
    use InteractsWithQueue;
    /**
     * Create the event listener.
     *
     * @return void
     */
    protected $teacherRepository;

    public function __construct(TeacherRepository $teacherRepository)
    {
        $this->teacherRepository = $teacherRepository;
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
            if(!empty($subject)){
                $teacher->subject = $subject;
                $teacher->phone_number = $phone;
                $teacher->email = $email;
                $teacher->save();
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
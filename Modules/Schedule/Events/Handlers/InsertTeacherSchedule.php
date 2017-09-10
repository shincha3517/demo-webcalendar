<?php
/**
 * Created by PhpStorm.
 * User: eric
 * Date: 8/25/17
 * Time: 11:39
 */
namespace Modules\Schedule\Events\Handlers;

use DebugBar\DebugBar;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Modules\Page\Events\PageIsCreating;
use Modules\Schedule\Events\ImportExcelSchedule;
use Modules\Schedule\Repositories\ScheduleRepository;
use Modules\Schedule\Repositories\TeacherRepository;

class InsertTeacherSchedule implements ShouldQueue
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
    protected $scheduleRepository;

    public function __construct(TeacherRepository $teacherRepository, ScheduleRepository $scheduleRepository)
    {
        $this->teacherRepository = $teacherRepository;
        $this->scheduleRepository = $scheduleRepository;
    }

    /**
     * Handle the event.
     *
     * @param  ImportExcelSchedule  $event
     * @return void
     */
    public function handle(ImportExcelSchedule $event)
    {
        if (true) {
            $this->release(10);
        }


        $path = $event->path;
        $limitRow = $event->perRow;
        $limitRunRow = $event->limitRunRow;

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
        sleep(2);
    }
}
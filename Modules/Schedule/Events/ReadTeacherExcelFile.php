<?php
/**
 * Created by PhpStorm.
 * User: eric
 * Date: 8/25/17
 * Time: 11:25
 */

namespace Modules\Schedule\Events;

use Illuminate\Queue\SerializesModels;

class ReadTeacherExcelFile
{
    use SerializesModels;

    public $rowNumber;
    public $interval;
    public $startTime;

    /**
     * Create a new event instance.
     *
     * @param  Excel Path  $path
     * @return void
     */
    public function __construct($rowNumber, $interval, $startTime)
    {
        $this->rowNumber = $rowNumber;
        $this->interval = $interval;
        $this->startTime=$startTime;
    }
}
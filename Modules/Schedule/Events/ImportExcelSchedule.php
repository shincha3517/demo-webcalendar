<?php
/**
 * Created by PhpStorm.
 * User: eric
 * Date: 8/25/17
 * Time: 11:25
 */

namespace Modules\Schedule\Events;

use Illuminate\Queue\SerializesModels;

class ImportExcelSchedule
{
    use SerializesModels;

    public $path;
    public $perRow;
    public $limitRunRow;
    public $interval;
    public $startTime;

    /**
     * Create a new event instance.
     *
     * @param  Excel Path  $path
     * @return void
     */
    public function __construct($path , $perRow = 10 , $limitRunRow, $interval, $startTime)
    {
        $this->path = $path;
        $this->perRow = $perRow;
        $this->limitRunRow = $limitRunRow;
        $this->interval = $interval;
        $this->startTime=$startTime;
    }
}
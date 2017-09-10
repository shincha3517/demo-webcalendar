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

    /**
     * Create a new event instance.
     *
     * @param  Excel Path  $path
     * @return void
     */
    public function __construct($path , $perRow = 10 , $limitRunRow)
    {
        $this->path = $path;
        $this->perRow = $perRow;
        $this->limitRunRow = $limitRunRow;
    }
}
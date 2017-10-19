<?php
/**
 * Created by PhpStorm.
 * User: eric
 * Date: 8/25/17
 * Time: 11:25
 */

namespace Modules\Schedule\Events;

use Illuminate\Queue\SerializesModels;

class ReadSubjectSheet
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
    public function __construct($rowNumber)
    {
        $this->rowNumber = $rowNumber;
    }
}
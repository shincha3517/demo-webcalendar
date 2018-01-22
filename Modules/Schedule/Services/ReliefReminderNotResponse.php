<?php
/**
 * Created by PhpStorm.
 * User: eric
 * Date: 1/19/18
 * Time: 8:51 AM
 */

namespace Modules\Schedule\Services;


use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\DB;
use Modules\Schedule\Entities\Assignment;
use Modules\Schedule\Entities\Teacher;
use Modules\Schedule\Helpers\SendSMS;
use Modules\Schedule\Repositories\AssignmentRepository;

class ReliefReminderNotResponse
{
    private $assignment;
    public $jobs;
    /**
     * Construct a new AssignmentRepository
     *
     * @param Illuminate\Support\Collection $twilioClient The client to use to query the API
     */
    public function __construct()
    {

    }

    /**
     * Send reminders for each job
     *
     * @return void
     */
    public function sendReminders()
    {
//        $this->jobs = Assign ();

        $this->jobs = Assignment::where('is_past',0)->where('notify_status',0)->groupBy('code')->get();

        foreach($this->jobs as $job){
            $this->_remindAbout($job);
        }
    }

    /**
     * Sends a message for an $job
     *
     * @param Assignment $job The appointment to remind
     *
     * @return void
     */
    private function _remindAbout($job)
    {
        if(!empty($job->notify_at)){
            $notifyDate =  Carbon::parse($job->notify_at);
            if($notifyDate->toDateString() == Carbon::now()->toDateString()){
                if($notifyDate->diffInHours(Carbon::now()) == 0 &&
                    $notifyDate->diffInMinutes(Carbon::now()) == 0 ){
                    //send reminder SMS
                    if($job->created_by != null){
                        $receiver = Teacher::where('email', $job->created_by)->first();
                        if($receiver){
                            $body = 'Dear '.$receiver->name .','. $job->replaced_teacher_name .' has not replied job #' . $job->code.'';
                            SendSMS::send($receiver->phone_number, $body);
                        }else{
                            $body = 'Dear admin,'. $job->replaced_teacher_name .' has not replied job #' . $job->code.'';
                            SendSMS::send(false, $body);
                        }
                    }else{
                        $body = 'Dear admin,'. $job->replaced_teacher_name .' has not replied job #' . $job->code.'';
                        SendSMS::send(false, $body);
                    }

                    DB::table('makeit__assignment')->where('code',$job->code)->update(['notify_status'=>1]);
                }
            }
        }

    }
}
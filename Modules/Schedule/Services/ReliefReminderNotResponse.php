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
use Modules\Schedule\Entities\Assignment;
use Modules\Schedule\Entities\Teacher;
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

        $this->jobs = Assignment::where('is_past',0)->where('notify_status',0)->get();

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
                    if($job->created_by!=null){
                        $receiver = Teacher::where('email', $job->created_by)->first();
                        if($receiver){
                            $body = 'Dear '.$receiver->name .','. $job->replaced_teacher_name .' has not replied job #' . $job->code.'';
                            $this->_sendSMS($receiver->phone_number, $body);
                        }else{
                            $body = 'Dear admin,'. $job->replaced_teacher_name .' has not replied job #' . $job->code.'';
                            $this->_sendSMS(false, $body);
                        }
                    }else{
                        $body = 'Dear admin,'. $job->replaced_teacher_name .' has not replied job #' . $job->code.'';
                        $this->_sendSMS(false, $body);
                    }
                    
                    $job->notify_status = 1;
                    $job->save();
                }
            }
        }

    }
    /**
     * Sends a single message using the app's global configuration
     *
     * @param string $number  The number to message
     * @param string $content The content of the message
     *
     * @return void
     */
    private function _sendSMS($toNumber, $body)
    {
        $username = env('TAR_USERNAME');
        $pwd = env('TAR_PASSWORD');
        $tarNumber = $toNumber ? '65'.$toNumber : env('ADMIN_NUMBER');
        $tarBody = urlencode($body);
        $messageId = Carbon::today()->timestamp;

        try {
            $client = new Client(); //GuzzleHttp\Client
//            $request = 'http://www.sendquickasp.com/client_api/index.php?username=yuhuasec&passwd=pass1234&tar_num=84986981718&tar_msg=Test&callerid=6584376346&route_to=api_send_sms';
            $request = 'http://www.sendquickasp.com/client_api/index.php?username='.$username.'&passwd='.$pwd.'&tar_num='.$tarNumber.'&tar_msg='.$tarBody.'&callerid=6584376346&route_to=api_send_sms';

            $sendSMSRequest = $client->get($request);
            $sendSMSResut = $sendSMSRequest->getBody()->getContents();
            if(strpos($sendSMSResut,'sent')){
                return true;
            }else{
                return false;
            }
        }catch (GuzzleException $error) {
            echo $error->getMessage();exit;
        }
    }
}
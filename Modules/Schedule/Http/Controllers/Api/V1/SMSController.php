<?php

namespace Modules\Schedule\Http\Controllers\Api\V1;

use Carbon\Carbon;
use Faker\Factory;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\CompliancePortal\Entities\CompanyControl;
use Modules\CompliancePortal\Entities\CompanyEvidence;
use Modules\CompliancePortal\Entities\Compliance;
use Modules\CompliancePortal\Entities\Control;
use Modules\CompliancePortal\Entities\CrossCompliance;
use Modules\CompliancePortal\Entities\Project;
use Modules\Schedule\Entities\Assignment;
use Modules\Schedule\Entities\Teacher;

class SMSController extends Controller
{
    public function receiveSMSReply(Request $request){
        $result = [];
        if($request->has(['mno','txt','dtm','charset'])){

            $phoneNumber = substr($request->get('mno'),3,10);
            $text = explode(' ',$request->get('txt'));

            $teacher = Teacher::where('phone_number',$phoneNumber)->get()->first();
            if($teacher){
                if(count($text)){
                    $jobCode = (int) trim($text[1]);
                    $status = 0;
                    $reply = '';
                    if($text[0] == 'Yes' || $text[0]== 'YES'){
                        $status  = 1;
                        $reply = 'Confirmed';
                    }elseif($text[0] == 'No' || $text[0]== 'NO'){
                        $status  = 2;
                        $reply = 'Rejected';
                    }

                    //update confirm SMS
                    Assignment::where('code',$jobCode)->update(['status'=>$status]);

                    $job = Assignment::where('code',$jobCode)->get()->first();
                    if($job){
                        //send confirm SMS
                        $body = $job->replaced_teacher_name.' has '.$reply .' #'.$text[1];
                        $this->_sendSMS($job->teacher->phone_number,$body);
                    }

                    $result = [
                        'status'=> true,
                        'message'=> 'update data successful',
                        'data'=> $request->all()
                    ];
                }
            }
        }

        if(empty($result)){
            $result = [
                'status'=> false,
                'message'=> 'update data failure',
                'data'=> $request->all()
            ];
        }


        DB::table('api_logs')->insert([
            'method'=>$request->method(),
            'request_url'=> $request->url(),
            'request_string'=> json_encode($request->all()),
            'response_string'=> json_encode($result),
            'request_ip'=> $request->ip(),
            'request_header'=> $request->headers,
        ]);
        return response()->json($result);
    }

    public function _sendSMS($toNumber,$body){
        $username = env('TAR_USERNAME');
        $pwd = env('TAR_PASSWORD');
        $tarNumber = '65'.$toNumber;
        $tarBody = urlencode($body);
        $messageId = Carbon::today()->timestamp;

        try {
            $client = new Client(); //GuzzleHttp\Client
//            $request = 'http://www.sendquickasp.com/client_api/index.php?username=yuhuasec&passwd=pass1234&tar_num=84986981718&tar_msg=Test&callerid=6584376346&route_to=api_send_sms';
            $request = 'http://www.sendquickasp.com/client_api/index.php?username='.$username.'&passwd='.$pwd.'&tar_num='.$tarNumber.'&tar_msg='.$tarBody.'&callerid=6584376346&route_to=api_send_sms';

            $sendSMSRequest = $client->get($request);
            $sendSMSResut = $sendSMSRequest->getBody()->getContents();
            if(strpos($sendSMSResut,'sent')){
                dd($sendSMSResut);
                return true;
            }else{
                return false;
            }
        }
        catch (GuzzleException $error) {
            echo $error->getMessage();exit;
        }
    }
}

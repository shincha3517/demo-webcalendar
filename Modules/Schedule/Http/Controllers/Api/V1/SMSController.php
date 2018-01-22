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
use Illuminate\Support\Facades\Log;
use Modules\CompliancePortal\Entities\CompanyControl;
use Modules\CompliancePortal\Entities\CompanyEvidence;
use Modules\CompliancePortal\Entities\Compliance;
use Modules\CompliancePortal\Entities\Control;
use Modules\CompliancePortal\Entities\CrossCompliance;
use Modules\CompliancePortal\Entities\Project;
use Modules\Schedule\Entities\Assignment;
use Modules\Schedule\Entities\Teacher;
use Modules\Schedule\Helpers\SendSMS;

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

                        //created job phone number
                        $userAssign = Teacher::where('email',$job->created_by)->first();
                        if($userAssign){
                            SendSMS::send($userAssign->phone_number,$body);
                        }else{
                            SendSMS::send(false,$body);
                            Log::error('Can not find email'.$job->created_by);
                        }
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
}

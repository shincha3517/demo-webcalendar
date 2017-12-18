<?php

namespace Modules\Schedule\Http\Controllers\Api\V1;

use Faker\Factory;
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
        if($request->has(['mno','txt','dtm','charset'])){

            $phoneNumber = substr($request->get('mno'),3,10);
            $text = explode(' ',$request->get('txt'));

            $teacher = Teacher::where('phone_number',$phoneNumber)->get()->first();
            if($teacher){
                if(count($text)){
                    $jobCode = (int) trim($text[1]);
                    $status = 0;
                    if($text[0] == 'Yes' || $text[0]== 'YES'){
                        $status  = 1;
                    }elseif($text[0] == 'No' || $text[0]== 'NO'){
                        $status  = 2;
                    }
                    Assignment::where('code',$jobCode)->update(['status'=>$status]);
                }
            }

            $result = [
                'status'=> true,
                'message'=> 'update data successful',
                'data'=> $request->all()
            ];
        }else{
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

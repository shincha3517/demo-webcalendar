<?php
/**
 * Created by PhpStorm.
 * User: eric
 * Date: 1/22/18
 * Time: 10:19 AM
 */

namespace Modules\Schedule\Helpers;


use Carbon\Carbon;
use GuzzleHttp\Client;

class SendSMS
{
    public static function send($toNumber,$body){
        $username = env('TAR_USERNAME');
        $pwd = env('TAR_PASSWORD');
        if($toNumber != '84986981718' || $toNumber == false) {
            $tarNumber = $toNumber ? '65'.$toNumber : env('ADMIN_NUMBER');
        }else{
            $tarNumber = '84986981718';
        }

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
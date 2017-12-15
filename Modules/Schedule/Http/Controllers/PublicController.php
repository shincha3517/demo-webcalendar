<?php

namespace Modules\Schedule\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Core\Http\Controllers\BasePublicController;
use Modules\Menu\Repositories\MenuItemRepository;
use Modules\Page\Entities\Page;
use Modules\Page\Repositories\PageRepository;
use Modules\Schedule\Entities\Confirm;
use Modules\Schedule\Repositories\ScheduleRepository;

class PublicController extends BasePublicController
{
    /**
     * @var PageRepository
     */
    private $schedule;
    /**
     * @var Application
     */
    private $app;

    public function __construct(ScheduleRepository $schedule, Application $app)
    {
        parent::__construct();
        $this->schedule = $schedule;
        $this->app = $app;
    }

    /**
     * @param $slug
     * @return \Illuminate\View\View
     */
    public function confirm(Request $request)
    {
        // work with get or post
//        $request = array_merge($_GET, $_POST);

        if($request->has('to') || $request->has('msisdn') || $request->has('text')){
            $confirm = Confirm::create([
                'phone_number'=>$request->get('msisdn'),
                'body'=>$request->get('text'),
            ]);
            return true;
        }
        else{
            Log::info('not inbound message');
            echo 'not inbound message';
        }
    }

    public function receiveSMSReply(Request $request){
        if($request->has(['mno','txt','dtm','charset'])){

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

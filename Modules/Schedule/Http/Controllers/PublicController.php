<?php

namespace Modules\Schedule\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
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
}

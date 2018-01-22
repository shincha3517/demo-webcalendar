<?php

namespace Modules\Schedule\Events\Handlers;

use Maatwebsite\Sidebar\Badge;
use Maatwebsite\Sidebar\Group;
use Maatwebsite\Sidebar\Item;
use Maatwebsite\Sidebar\Menu;
use Modules\Core\Events\BuildingSidebar;
use Modules\Page\Repositories\PageRepository;
use Modules\Schedule\Repositories\TeacherRepository;
use Modules\User\Contracts\Authentication;

class RegisterScheduleSidebar implements \Maatwebsite\Sidebar\SidebarExtender
{
    /**
     * @var Authentication
     */
    protected $auth;

    /**
     * @param Authentication $auth
     *
     * @internal param Guard $guard
     */
    public function __construct(Authentication $auth)
    {
        $this->auth = $auth;
    }

    public function handle(BuildingSidebar $sidebar)
    {
        $sidebar->add($this->extendWith($sidebar->getMenu()));
    }

    /**
     * @param Menu $menu
     * @return Menu
     */
    public function extendWith(Menu $menu)
    {
        $menu->group(trans('schedule::schedule.makeit'), function (Group $group) {

            $group->item(trans('schedule::schedule.title'), function (Item $item) {
                $item->weight(10);
                $item->icon('fa fa-calendar');
                $item->authorize(
                    $this->auth->hasAccess('schedule.schedules.index') or $this->auth->hasAccess('schedule.schedules.upload') or $this->auth->hasAccess('schedule.schedules.worker')
                );

                $item->item(trans('schedule::schedule.upload-excel'), function (Item $item) {
                    $item->weight(11);
                    $item->icon('fa fa-file-excel-o');
                    $item->route('admin.schedule.upload.form');
                    $item->authorize(
                        $this->auth->hasAccess('schedule.schedules.upload')
                    );
                });

                $item->item(trans('schedule::schedule.assign-user'), function (Item $item) {
                    $item->weight(12);
                    $item->icon('fa fa-clock-o');
                    $item->route('admin.schedule.index');
                    $item->authorize(
                        $this->auth->hasAccess('schedule.schedules.index')
                    );
                });
                $item->item(trans('schedule::schedule.leave-system'), function (Item $item) {
                    $item->weight(13);
                    $item->icon('fa fa-clock-o');
                    $item->route('admin.schedule.worker');
                    $item->authorize(
                        $this->auth->hasAccess('schedule.schedules.worker')
                    );
                });

                $item->item(trans('schedule::report.report'), function (Item $item) {
                    $item->weight(14);
                    $item->icon('fa fa-clock-o');
                    $item->route('admin.schedule.report');
                    $item->authorize(
                        $this->auth->hasAccess('schedule.report.index')
                    );
                });
            });

        });

        if($this->auth->hasAccess('schedule.meeting.index')){
            $menu->group('Meeting', function (Group $group) {

                $group->item(trans('schedule::meeting.meeting'), function (Item $item) {
                    $item->weight(15);
                    $item->authorize(
                        $this->auth->hasAccess('schedule.meeting.index') or $this->auth->hasAccess('schedule.meeting.create')
                    );

                    $item->item(trans('schedule::meeting.list'), function (Item $item) {
                        $item->weight(16);
                        $item->icon('fa fa-file-excel-o');
                        $item->route('admin.schedule.meeting.index');
                        $item->authorize(
                            $this->auth->hasAccess('schedule.meeting.index')
                        );
                    });
                });


            });
        }



        if($this->auth->hasAccess('schedule.teacher.index')){

            $menu->group(trans('schedule::schedule.makeit'), function (Group $group) {
                $group->item(trans('schedule::teacher.list'), function (Item $item) {
                    $item->icon('fa fa-user-secret');
                    $item->weight(16);
                    $item->route('admin.schedule.teacher.index');
                    $item->badge(function (Badge $badge, TeacherRepository $teacher) {
                        $badge->setClass('bg-green');
                        $badge->setValue($teacher->all()->count());
                    });
                    $item->authorize(
                        $this->auth->hasAccess('schedule.teacher.index')
                    );
                });
            });
        }


        return $menu;
    }
}

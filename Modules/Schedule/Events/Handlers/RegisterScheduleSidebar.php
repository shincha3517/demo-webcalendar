<?php

namespace Modules\Schedule\Events\Handlers;

use Maatwebsite\Sidebar\Group;
use Maatwebsite\Sidebar\Item;
use Maatwebsite\Sidebar\Menu;
use Modules\Core\Events\BuildingSidebar;
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
                    $this->auth->hasAccess('schedule.schedules.index') or $this->auth->hasAccess('schedule.schedules.upload')
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
            });

        });

        return $menu;
    }
}
<?php

namespace Modules\Schedule\Entities;

use Illuminate\Database\Eloquent\Model;

class ScheduleDate extends Model
{

    protected $table = 'makeit__schedule_dates';
    protected $fillable = ['date','day_name','start_date','interval','old_total_timeslots','event_total_timeslots'];
}

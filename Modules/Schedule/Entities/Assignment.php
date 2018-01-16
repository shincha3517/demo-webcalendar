<?php

namespace Modules\Schedule\Entities;

use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{

    protected $table = 'makeit__assignment';
    protected $fillable = ['teacher_id','replaced_teacher_id','teacher_name','replaced_teacher_name','schedule_id','schedule_event_id','lesson','subject','start_date','end_date','slot_id','day_name','selected_date','reason','additionalRemark','schedule_type','is_past','status','code','created_by'];

    public function teacher(){
        return $this->belongsTo(Teacher::class);
    }

    public function replaceTeacher(){
        return $this->belongsTo(Teacher::class,'replaced_teacher_id');
    }

    public function schedule(){
        return $this->belongsTo(Schedule::class);
    }

    public function scheduleEvent(){
        return $this->belongsTo(ScheduleEvent::class);
    }
}

<?php

namespace Modules\Schedule\Entities;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{

    const ASSIGNED_STATUS = 1;
    const REPLACE_STATUS = 2;

    protected $table = 'makeit__activity_log';
    protected $fillable = ['teacher_id','replaced_teacher_id','schedule_id','selected_date','status'];

    public function teacher(){
        return $this->belongsTo(Teacher::class);
    }

    public function replaceTeacher(){
        return $this->belongsTo(Teacher::class,'replaced_teacher_id');
    }

    public function schedule(){
        return $this->belongsTo(Schedule::class);
    }
}

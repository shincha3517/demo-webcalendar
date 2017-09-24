<?php

namespace Modules\Schedule\Entities;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{

    protected $table = 'makeit__schedules';
    protected $fillable = ['teacher_id','date_id','subject_code','start_date','end_date','start_time','end_time'];

    public function teacher(){
        
        return $this->belongsTo(Teacher::class);
    }
}

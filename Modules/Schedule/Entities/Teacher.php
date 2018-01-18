<?php

namespace Modules\Schedule\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\User\Entities\Sentinel\User;

class Teacher extends Model
{

    protected $table = 'makeit__teachers';
    protected $fillable = ['id','name','phone_number','subject','email','user_id','teacher_type'];

    public function schedule(){
        return $this->belongsTo(Schedule::class,'id','teacher_id');
    }
    public function user(){
        return $this->hasOne(User::class,'id','user_id');
    }
}

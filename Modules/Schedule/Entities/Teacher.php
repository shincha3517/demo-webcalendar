<?php

namespace Modules\Schedule\Entities;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{

    protected $table = 'makeit__teachers';
    protected $fillable = ['name','phone_number'];
}

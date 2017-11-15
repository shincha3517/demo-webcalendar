<?php

namespace Modules\Schedule\Entities;

use Illuminate\Database\Eloquent\Model;

class Confirm extends Model
{

    protected $table = 'makeit__schedule_confirm';
    protected $fillable = ['phone_number','body'];
}

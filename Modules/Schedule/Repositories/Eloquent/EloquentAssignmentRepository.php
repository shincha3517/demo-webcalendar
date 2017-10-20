<?php

namespace Modules\Schedule\Repositories\Eloquent;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Core\Repositories\Eloquent\EloquentBaseRepository;
use Modules\Schedule\Entities\Assignment;
use Modules\Schedule\Repositories\AssignmentRepository;

class EloquentAssignmentRepository extends EloquentBaseRepository implements AssignmentRepository
{
    public function getReliefNumber($type, $selectedDate,$reliefTeacherId)
    {
//        DB::enableQueryLog();
        $query = $this->model->where('replaced_teacher_id',$reliefTeacherId)->where('is_past',0);
        if($type == 'date'){
            $query->where('selected_date',$selectedDate);
        }elseif($type == 'week'){
            $date = Carbon::now()->toDateString();

            $startWeek = Carbon::parse($date)->startOfWeek();
            $endWeek = Carbon::parse($date)->endOfWeek();

            $query->where('selected_date','>=',$startWeek);
            $query->where('selected_date','<=',$endWeek);
        }elseif($type == 'month'){
            $date = Carbon::now()->toDateString();

            $startMonth = Carbon::parse($date)->startOfMonth();
            $endMonth = Carbon::parse($date)->endOfMonth();

            $query->where('selected_date','>=',$startMonth);
            $query->where('selected_date','<=',$endMonth);
        }elseif($type == 'year'){
            $date = Carbon::now()->toDateString();

            $startYear = Carbon::parse($date)->startOfYear();
            $endYear = Carbon::parse($date)->endOfYear();

            $query->where('selected_date','>=',$startYear);
            $query->where('selected_date','<=',$endYear);
        }
        return $query->count();
//        dd(DB::getQueryLog());
    }
}

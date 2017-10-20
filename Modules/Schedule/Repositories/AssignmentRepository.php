<?php

namespace Modules\Schedule\Repositories;

use Modules\Core\Repositories\BaseRepository;

interface AssignmentRepository extends BaseRepository
{
    public function getReliefNumber($type,$selectedDate,$reliefTeacherId);
}

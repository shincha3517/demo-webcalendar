<?php

return [
// append
    'schedule.schedules' => [
        'upload' => 'schedule::schedules.upload excel',
        'index' => 'schedule::schedules.list schedule',
        'worker' => 'schedule::schedules.worker schedule',
    ],
    'schedule.report' => [
        'index' => 'schedule::report.list schedule',
        'export' => 'schedule::report.export schedule',
    ],
    'schedule.meeting' => [
        'index' => 'schedule::meeting.list meeting',
        'create' => 'schedule::meeting.create meeting',
        'edit' => 'schedule::meeting.create meeting',
    ],
    'schedule.teacher' => [
        'index' => 'schedule::teacher.list teachers',
        'edit' => 'schedule::teacher.edit teacher',
        'create' => 'schedule::teacher.create teacher',
        'delete' => 'schedule::teacher.delete teacher',
    ],
];

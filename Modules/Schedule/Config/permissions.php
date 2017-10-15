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
];

<?php

return [

//    'interval' => [
//        'description' => 'Intervals ',
//        'view' => 'schedule::fields.select-theme',
//        'translatable' => false,
//    ],
    'sorting_subject' => [
        'description' => 'Prioritise those within the same subject',
        'view' => 'checkbox',
        'translatable' => false,
        'default'=> 1
    ],
    'sorting_lesson' => [
        'description' => 'Prioritise User with fewer lessons before and after the assigned relief lesson',
        'view' => 'checkbox',
        'translatable' => false,
        'default'=> 1
    ],
    'sorting_number_relief' => [
        'description' => 'Prioritise User with number relief made by week, term, year',
        'view' => 'checkbox',
        'translatable' => false,
        'default'=> 1
    ],
];

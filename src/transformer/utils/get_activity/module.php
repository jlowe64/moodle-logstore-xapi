<?php

namespace transformer\utils\get_activity;

use transformer\utils as utils;

function module(array $config, $module_type, $module, $lang) {
    return [
        'id' => utils\get_activity_url($config, $module_type, $module->id),
        'definition' => [
            'type' => 'http://id.tincanapi.com/activitytype/lms/module',
            'name' => [
                $lang => $module->name,
            ],
        ],
    ];
}

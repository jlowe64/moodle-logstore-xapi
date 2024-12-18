<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Transformer utility for retrieving (assign submission) activities.
 *
 * @package   logstore_xapi
 * @copyright Milt Reder <milt@yetanalytics.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace src\transformer\utils\get_activity;

use src\transformer\utils as utils;

/**
 * Transformer utility for retrieving the assign submission.
 *
 * @param array $config The transformer config settings.
 * @param int $cmid The id of the context.
 * @param string $lang The language.
 * @return array
 */
function assign_submission(array $config, int $cmid, string $lang) {
    $repo = $config['repo'];
    $coursemodule = $repo->read_record_by_id('course_modules', $cmid);
    $module = $repo->read_record_by_id('modules', $coursemodule->module);
    $instance = $repo->read_record_by_id('assign', $coursemodule->instance);
    $instancename = property_exists($instance, 'name') ? $instance->name : $module->name;

    return [
        ...base(),
        'id' => $config['app_url']
            . '/mod/assign/view.php?id='
            . $cmid
            . '#submission',
        'definition' => [
            'type' => 'https://xapi.edlm/profiles/edlm-lms/concepts/activity-types/submission',
            'name' => [
                $lang => $instancename . ' Submission'
            ]
        ]
    ];
}

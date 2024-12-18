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
 * Transformer utility for retrieving course group activities.
 *
 * @package   logstore_xapi
 * @copyright Milt Reder <milt@yetanalytics.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace src\transformer\utils\get_activity;

use src\transformer\utils as utils;

/**
 * Transformer for course group xAPI activity.
 *
 * @param array $config The transformer config settings.
 * @param \stdClass $course The course object.
 * @param \stdClass $group The group object.
 * @return array
 */
function course_group(array $config, \stdClass $course, \stdClass $group) {
    $coursename = $course->fullname ? $course->fullname : 'A Moodle course';
    $courselang = utils\get_course_lang($course);

    $activity = [
        ...base(),
        'id' => $config['app_url'] . '/group/index.php?id=' . $group->id,
        'definition' => [
            'type' => 'https://xapi.edlm/profiles/edlm-lms/concepts/activity-types/group',
        ],
    ];

    // moodle groups only have names when they aren't deleted
    if (isset($group->name)) {
        $activity['definition']['name'] = [
            $courselang => $group->name,
        ];
    }

    return $activity;
}

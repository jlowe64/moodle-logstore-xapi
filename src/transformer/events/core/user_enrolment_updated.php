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
 * Transform for user enrolment updated event.
 *
 * @package   logstore_xapi
 * @copyright Milt Reder <milt@yetanalytics.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace src\transformer\events\core;

use src\transformer\utils as utils;

/**
 * Transformer for the user enrolment updated event.
 *
 * @param array $config The transformer config settings.
 * @param \stdClass $event The event to be transformed.
 * @return array
 */
function user_enrolment_updated(array $config, \stdClass $event) {
    $repo = $config['repo'];
    $enrolment = $repo->read_record_by_id('user_enrolments', $event->objectid);
    $user = $repo->read_record_by_id('user', $event->userid);
    $cuser = $repo->read_record_by_id('user', $event->relateduserid);
    $course = $repo->read_record_by_id('course', $event->courseid);
    $lang = utils\get_course_lang($course);

    if ($enrolment->status == 1) {
        $verb = [
            'id' => 'https://w3id.org/xapi/tla/verbs/suspended',
            'display' => [
                'en' => 'Suspended',
            ],
        ];
    } else {
        $verb = [
            'id' => 'https://w3id.org/xapi/tla/verbs/resumed',
            'display' => [
                'en' => 'Resumed',
            ],
        ];
    }

    return [[
        'actor' => utils\get_user($config, $cuser),
        'verb' => $verb,
        'object' => utils\get_activity\course($config, $course),
        'context' => utils\get_enrolment_context(
            $config,
            $event,
            $course,
            $cuser,
            $user,
            $lang
        ),
    ]];

}

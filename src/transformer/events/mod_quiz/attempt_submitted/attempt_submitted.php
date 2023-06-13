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
 * Transform for the quiz attempt submitted event.
 *
 * @package   logstore_xapi
 * @copyright Jerret Fowler <jerrett.fowler@gmail.com>
 *            Ryan Smith <https://www.linkedin.com/in/ryan-smith-uk/>
 *            David Pesce <david.pesce@exputo.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace src\transformer\events\mod_quiz\attempt_submitted;

use Exception;
use src\transformer\utils as utils;

/**
 * Transformer for quiz attempt submitted event.
 *
 * @param array $config The transformer config settings.
 * @param \stdClass $event The event to be transformed.
 * @return array
 */

function attempt_submitted(array $config, \stdClass $event): array {

    $repo = $config['repo'];
    $userid = $event->relateduserid;
    if ($userid < 2) {
        $userid = 1;
    }
    $user = $repo->read_record_by_id('user', $userid);
    try {
        $course = $repo->read_record_by_id('course', $event->courseid);
    } catch (Exception $e) {
        // OBJECT_NOT_FOUND.
        $course = $repo->read_record_by_id('course', 1);
    }
    $lang = utils\get_course_lang($course);
    $other = unserialize($event->other);
    if (!$other) {
        $other = json_decode($event->other);
        $quizid = (int)$other->quizid;
    } else {
        $quizid = $other['quizid'];
    }

    try {
        $attempt = $repo->read_record_by_id('quiz_attempts', $event->objectid);
        $gradeitem = $repo->read_record('grade_items', [
            'itemmodule' => 'quiz',
            'iteminstance' => $quizid,
        ]);
        $attemptgrade = $repo->read_record('grade_grades', [
            'itemid' => $gradeitem->id,
            'userid' => $event->relateduserid
        ]);

        $result = utils\get_attempt_result($attempt, $gradeitem, $attemptgrade);

    } catch (Exception $e) {
        // OBJECT_NOT_FOUND.
        $result = [
            'score' => [
                'raw' => 0.0,
                'min' => 0.0,
                'max' => 0.0,
                'scaled' => 0.0,
            ],
            'completion' => false,
            'success' => false,
        ];
    }

    return [[
        'actor' => utils\get_user($config, $user),
        'verb' => utils\get_verb('completed', $config, $lang),
        'object' => utils\get_activity\quiz_attempt($config, $event->objectid, $event->contextinstanceid),
        'timestamp' => utils\get_event_timestamp($event),
        'result' => $result,
        'context' => [
            'platform' => $config['source_name'],
            'language' => $lang,
            'extensions' => utils\extensions\base($config, $event, $course),
            'contextActivities' => [
                'grouping' => [
                    utils\get_activity\site($config),
                    utils\get_activity\course($config, $course),
                    utils\get_activity\course_quiz($config, $course, $event->contextinstanceid),
                ],
                'category' => [
                    utils\get_activity\source($config),
                ]
            ],
        ]
    ]];
}

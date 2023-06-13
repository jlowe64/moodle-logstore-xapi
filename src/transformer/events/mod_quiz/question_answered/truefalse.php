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
 * Transform for the quiz question (truefalse) answered event.
 *
 * @package   logstore_xapi
 * @copyright Jerret Fowler <jerrett.fowler@gmail.com>
 *            Ryan Smith <https://www.linkedin.com/in/ryan-smith-uk/>
 *            David Pesce <david.pesce@exputo.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace src\transformer\events\mod_quiz\question_answered;

use Exception;
use src\transformer\utils as utils;

/**
 * Transformer for quiz question (truefalse) answered event.
 *
 * @param array $config The transformer config settings.
 * @param \stdClass $event The event to be transformed.
 * @param \stdClass $questionattempt The questionattempt object.
 * @param \stdClass $question The question object.
 * @return array
 */
function truefalse(array $config, \stdClass $event, \stdClass $questionattempt, \stdClass $question) {

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
    $cmid = $event->contextinstanceid;
    $lang = utils\get_course_lang($course);
    $attemptid = $event->objectid;
    $questionid = is_null($question->id) ? 0 : $question->id;
    $name = is_null($question->name) ? '' : $question->name;
    $questiontext = utils\get_string_html_removed($question->questiontext);
    $responsesummary = is_null($questionattempt->responsesummary) ? '' : $questionattempt->responsesummary;
    $rightanswer = is_null($questionattempt->rightanswer) ? '' : $questionattempt->rightanswer;

    return [[
        'actor' => utils\get_user($config, $user),
        'verb' => [
            'id' => 'http://adlnet.gov/expapi/verbs/answered',
            'display' => [
                $lang => 'answered'
            ],
        ],
        'object' => [
            'id' => utils\get_quiz_question_id($config, $cmid, $questionid),
            'definition' => [
                'type' => 'http://adlnet.gov/expapi/activities/cmi.interaction',
                'name' => [
                    $lang => $name,
                ],
                'interactionType' => 'true-false',
                'description' => [
                    $lang => $questiontext,
                ],
            ]
        ],
        'timestamp' => utils\get_event_timestamp($event),
        'result' => [
            'response' => $responsesummary,
            'completion' => $responsesummary !== null,
            'success' => $rightanswer === $responsesummary,
            'extensions' => [
                'http://learninglocker.net/xapi/cmi/true-false/response' => $responsesummary === 'True',
            ],
        ],
        'context' => [
            'platform' => $config['source_name'],
            'language' => $lang,
            'extensions' => utils\extensions\base($config, $event, $course),
            'contextActivities' => [
                'grouping' => [
                    utils\get_activity\site($config),
                    utils\get_activity\course($config, $course),
                    utils\get_activity\course_quiz($config, $course, $cmid),
                    utils\get_activity\quiz_attempt($config, $attemptid, $cmid),
                ],
                'category' => [
                    utils\get_activity\source($config),
                ]
            ],
        ]
    ]];
}

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
 * Transformer utility for retrieving (Questionnaire All Responses Report) activities.
 *
 * @package   logstore_xapi
 * @copyright Cliff Casey <cliff@yetanalytics.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace src\transformer\utils\get_activity;

use src\transformer\utils as utils;

/**
 * Transformer utility for retrieving (Questionnaire All Responses Report) activities.
 *
 * @param array $config The transformer config settings.
 * @param \stdClass $course The course object.
 * @param \stdClass $questionnaire The questionnaire object.
 * @return array
 */
function questionnaire_all_responses_report(array $config, \stdClass $course, \stdClass $questionnaire) {
    $courselang = utils\get_course_lang($course);
    $reporturl = $config['app_url'].'/mod/questionnaire/report.php?instance='.$questionnaire->id.'&group=0';

    $activity = [
        ...base(),
        'id' => $reporturl,
        'definition' => [
            'type' => 'https://xapi.edlm/profiles/edlm-lms/concepts/activity-types/report'
        ],
    ];

    if (isset($questionnaire->name)) {
        $activity['definition']['name'] = [
            $courselang => $questionnaire->name.' Report'
        ];
    }

    return $activity;
}

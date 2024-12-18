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
 * Transformer utility for retrieving (lesson) activities.
 *
 * @package   logstore_xapi
 * @copyright Cliff Casey <cliff@yetanalytics.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace src\transformer\utils\get_activity;

use src\transformer\utils as utils;

/**
 * Transformer utility for retrieving (lesson) activities.
 *
 * @param array $config The transformer config settings.
 * @param \stdClass $course The course object.
 * @param \stdClass $entry The lesson entry object.
 * @param int $cmid The course module ID.
 * @return array
 */
function lesson(array $config, \stdClass $course, \stdClass $entry, int $cmid) {
    $courselang = utils\get_course_lang($course);
    $entryurl = $config['app_url'].'/mod/lesson/view.php?id='.$cmid.'#lesson';

    $activity = [
        ...base(),
        'id' => $entryurl,
        'definition' => [
            'type' => 'https://xapi.edlm/profiles/edlm-lms/concepts/activity-types/lesson-session'
        ],
    ];

    if (isset($entry->name)) {
        $activity['definition']['name'] = [
            $courselang => $entry->name
        ];
    }
    if (isset($entry->intro)) {
        $activity['definition']['description'] = [
            $courselang => utils\get_string_html_removed($entry->intro),
        ];
    }

    return $activity;
}

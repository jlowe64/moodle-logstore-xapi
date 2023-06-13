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
 * Transformer utility for retrieving search term data.
 *
 * @package   logstore_xapi
 * @copyright 2023 Daniela Rotelli <danielle.rotelli@gmail.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace src\transformer\utils\get_activity;

use src\transformer\utils as utils;

/**
 * Transformer utility for retrieving search term data.
 *
 * @param array $config The transformer config settings.
 * @param string $lang The language of the course.
 * @param \stdClass $course The course object.
 * @param string $term The searched term.
 * @return array
 */
function search_term(array $config, string $lang, \stdClass $course, string $term): array {

    $url = $config['app_url'] . 'mod/forum/search.php?id=' . $course->id . '&search=' . $term;

    $object = [
        'id' => $url,
        'definition' => [
            'type' => 'http://id.tincanapi.com/activitytype/tag',
            'name' => [
                $lang => 'term ' . $term,
            ],
            'description' => [
                $lang => 'the searched query',
            ],
        ],
    ];

    if (utils\is_enabled_config($config, 'send_short_course_id')) {
        $lmsshortid = 'https://w3id.org/learning-analytics/learning-management-system/short-id';
        $object['definition']['extensions'][$lmsshortid] = $course->shortname;
    }

    if (utils\is_enabled_config($config, 'send_course_and_module_idnumber')) {
        $courseidnumber = property_exists($course, 'idnumber') ? $course->idnumber : null;
        $lmsexternalid = 'https://w3id.org/learning-analytics/learning-management-system/external-id';
        $object['definition']['extensions'][$lmsexternalid] = $courseidnumber;
    }

    return $object;
}

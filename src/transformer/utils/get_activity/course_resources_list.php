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
 * Transformer utility for retrieving course resources list data.
 *
 * @package   logstore_xapi
 * @copyright 2023 Daniela Rotelli <danielle.rotelli@gmail.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace src\transformer\utils\get_activity;

/**
 * Transformer utility for retrieving course resources list data.
 *
 * @param array $config The transformer config settings.
 * @param \stdClass $course The course object.
 * @param string $lang The language of the course.
 * @return array
 */
function course_resources_list(array $config, \stdClass $course, string $lang): array {

    $name = property_exists($course, 'fullname') ? $course->fullname : 'A Moodle course';

    $object = [
                  'id' => $config['app_url'].'/course/resources.php?id='.$course->id,
                  'definition' => [
                      'type' => 'http://vocab.xapi.fr/activities/resources',
                      'name' => [
                          $lang => $name . ' resource list',
                      ],
                      'description' => [
                          $lang => 'the resource list of the course',
                      ],
                  ],
              ];

    if (array_key_exists('send_short_course_id', $config)) {
        $lmsshortid = 'https://w3id.org/learning-analytics/learning-management-system/short-id';
        $object['definition']['extensions'][$lmsshortid] = $course->shortname;
    }

    if (array_key_exists('send_course_and_module_idnumber', $config)) {
        $courseidnumber = property_exists($course, 'idnumber') ? $course->idnumber : null;
        $lmsexternalid = 'https://w3id.org/learning-analytics/learning-management-system/external-id';
        $object['definition']['extensions'][$lmsexternalid] = $courseidnumber;
    }

    return $object;
}

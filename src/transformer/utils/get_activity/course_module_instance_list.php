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
 * Transformer utility for retrieving course module instance list data.
 *
 * @package   logstore_xapi
 * @copyright 2023 Daniela Rotelli <danielle.rotelli@gmail.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

namespace src\transformer\utils\get_activity;

use src\transformer\utils as utils;

/**
 * Transformer utility for retrieving course module instance list data.
 *
 * @param array $config The transformer config settings.
 * @param \stdClass $course The course object.
 * @param string $coursemodule The module of the course.
 * @param string $lang The language of the course.
 * @return array
 */
function course_module_instance_list(array $config, \stdClass $course, string $coursemodule, string $lang): array {

    $coursemodule = explode('_', $coursemodule)[1];
    $url = $config['app_url'].'/mod/'.$coursemodule.'/index.php?id='.$course->id;

    $object = [
        'id' => $url,
        'definition' => [
            'type' => 'http://id.tincanapi.com/activitytype/collection-simple',
            'name' => [
                $lang => 'list of module instances',
            ],
            'description' => [
                $lang => 'list of instances for the course module ' . $coursemodule,
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

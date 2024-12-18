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
 * Transformer utility for retrieving course sections.
 *
 * @package   logstore_xapi
 * @copyright Milt Reder <milt@yetanalytics.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace src\transformer\utils\get_activity;

use src\transformer\utils as utils;

/**
 * Transformer utility for retrieving course sections.
 *
 * @param array $config The transformer config settings.
 * @param \stdClass $course The course object.
 * @param int $csid The course section id.
 * @return array
 */
function course_section(array $config, \stdClass $course, int $csid) {
    $repo = $config['repo'];
    $lang = utils\get_course_lang($course);
    $section = $repo->read_record_by_id('course_sections', $csid);

    return [
        ...base(),
        'id' => $config['app_url'] . '/course/section.php?id=' . $section->id,
        'definition' => [
            'type' => 'http://id.tincanapi.com/activitytype/section',
            'name' => [
                $lang => $course->fullname . ' Section ' . $section->section,
            ],
        ],
    ];
}

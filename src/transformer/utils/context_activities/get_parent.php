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
 * Transformer utility for retrieving the contextActivities parent array.
 *
 * @package   logstore_xapi
 * @copyright Milt Reder <milt@yetanalytics.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace src\transformer\utils\context_activities;

use src\transformer\utils as utils;

/**
 * Given a course module id, return the contextActivities parent array.
 *
 * @param array $config The transformer config.
 * @param int $cmid The course module ID.
 * @param ?bool $include_module Whether or not to include the course module in the array. Defaults to false.
 * @return array
 */
function get_parent(array $config, int $cmid, ?bool $include_module = false) {
    $repo = $config['repo'];
    $coursemodule = $repo->read_record_by_id('course_modules', $cmid);
    $course = $repo->read_record_by_id('course', $coursemodule->course);

    $parent = [
        utils\get_activity\course_section($config, $course, $coursemodule->section),
        utils\get_activity\course($config, $course),
    ];

    if ($include_module) {
        $parent = array_merge([
            utils\get_activity\course_module($config, $course, $cmid),
        ], $parent);
    }

    return $parent;
}

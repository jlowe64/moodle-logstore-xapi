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
 * Transformer utility for retrieving assignment assessable data.
 *
 * @package   logstore_xapi
 * @copyright 2023 Daniela Rotelli <danielle.rotelli@gmail.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace src\transformer\utils\get_activity;

use Exception;

/**
 * Transformer utility for retrieving assignment assessable data.
 *
 * @param array $config The transformer config settings.
 * @param string $lang The language of the course.
 * @param int $cmid The course module id.
 * @param string $component The type of component.
 * @return array
 */

function assignment_assessable(array $config, string $lang, int $cmid, string $component): array {

    if ($component == 'assignsubmission_file') {
        $type = 'http://activitystrea.ms/schema/1.0/file';
        $name = 'file';
    } else {
        $type = 'http://activitystrea.ms/schema/1.0/article';
        $name = 'online text';
    }

    $url = $config['app_url'] . '/mod/assign/view.php?id=' . $cmid;

    try {
        $repo = $config['repo'];
        $coursemodule = $repo->read_record_by_id('course_modules', $cmid);
        $status = $coursemodule->deletioninprogress;
        if ($status == 0) {
            $description = 'the assignment assessable';
        } else {
            $description = 'deletion in progress';
        }
    } catch (Exception $e) {
        // OBJECT_NOT_FOUND.
        $description = 'deleted ' . $e;
    }

    return [
        'id' => $url,
        'definition' => [
            'type' => $type,
            'name' => [
                $lang => $name,
            ],
            'description' => [
                $lang => $description,
            ],
        ],
    ];
}

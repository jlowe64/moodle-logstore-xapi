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
 * Transformer utility for retrieving grade item data.
 *
 * @package   logstore_xapi
 * @copyright 2023 Daniela Rotelli <danielle.rotelli@gmail.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace src\transformer\utils\get_activity;

use Exception;
use src\transformer\utils as utils;

/**
 * Transformer utility for retrieving grade item data.
 *
 * @param array $config The transformer config settings.
 * @param \stdClass $course The course object.
 * @param string $lang The language of the course.
 * @param int $itemid The id of the grade item.
 * @return array
 */

function grade_item(array $config, \stdClass $course, string $lang, int $itemid): array {

    $url = $config['app_url'] . '/grade/edit/tree/index.php?id=' . $course->id;

    try {
        $repo = $config['repo'];
        $item = $repo->read_record_by_id('grade_items', $itemid);
        $name = property_exists($item, 'itemname') ? $item->itemname : 'Grade item';
        $description = property_exists($item, 'iteminfo') ? $item->iteminfo : 'Grade item info';
        if (is_null($description)) {
            $description = '';
        }
        if (is_null($name)) {
            $name = 'Grade item';
        }
    } catch (Exception $e) {
        // OBJECT_NOT_FOUND.
        $name = 'item id ' . $itemid;
        $description = 'deleted';
    }

    $object = [
        'id' => $url,
        'definition' => [
            'type' => 'http://www.tincanapi.co.uk/activitytypes/grade_classification',
            'name' => [
                $lang => $name,
            ],
            'description' => [
                $lang => $description,
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

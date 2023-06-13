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
 * Transformer utility for retrieving attendance data.
 *
 * @package   logstore_xapi
 * @copyright 2023 Daniela Rotelli <danielle.rotelli@gmail.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace src\transformer\utils\get_activity;

use Exception;

/**
 * Transformer utility for retrieving attendance data.
 *
 * @param array $config The transformer config settings.
 * @param string $cmid The id of the course module.
 * @param string $other The field other of the event.
 * @param string $lang The language of the course.
 * @return array
 */

function attendance(array $config, string $cmid, string $other, string $lang): array {

    $other = unserialize($other);
    if (!$other) {
        $other = json_decode($other);
        $sessionid = (int)$other->sessionid;
        $grouptype = (int)$other->grouptype;
    } else {
        $sessionid = $other['sessionid'];
        $grouptype = $other['grouptype'];
    }

    try {
        $repo = $config['repo'];
        $coursemodule = $repo->read_record_by_id('course_modules', $cmid);
        $status = $coursemodule->deletioninprogress;
        if ($status == 0) {
            $description = 'the attendance activity';
        } else {
            $description = 'deletion in progress';
        }
    } catch (Exception $e) {
        // OBJECT_NOT_FOUND.
        $description = 'deleted ';
    }

    $url = $config['app_url'] . '/take.php?id=' . $cmid . '&sessionid=' . $sessionid . '&grouptype=' . $grouptype;

    return [
        'id' => $url,
        'definition' => [
            'type' => 'http://vocab.xapi.fr/activities/registration',
            'name' => [
                $lang => 'Attendance',
            ],
            'description' => [
                $lang => $description,
            ],
        ],
    ];
}

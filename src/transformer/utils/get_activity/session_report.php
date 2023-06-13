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
 * Transformer utility for retrieving session report data.
 *
 * @package   logstore_xapi
 * @copyright 2023 Daniela Rotelli <danielle.rotelli@gmail.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace src\transformer\utils\get_activity;

use Exception;

/**
 * Transformer utility for retrieving session report data.
 *
 * @param array $config The transformer config settings.
 * @param string $cmid The id of the course module.
 * @param string $other The field other of the event.
 * @param string $lang The language of the course.
 * @return array
 */

function session_report(array $config, string $cmid, string $other, string $lang): array {

    $other = unserialize($other);
    if (!$other) {
        $other = json_decode($other);
        $studentid = empty($other->studentid) ? '' : $other->studentid;
        $mode = empty($other->mode) ? '' : $other->mode;
        $view = empty($other->view) ? '' : $other->view;
        $groupby = empty($other->groupby) ? '' : $other->groupby;
        $sesscourses = empty($other->sesscourses) ? '' : $other->sesscourses;
        $curdate = empty($other->curdate) ? '' : $other->curdate;
    } else {
        $studentid = empty($other['studentid']) ? '' : $other['studentid'];
        $mode = empty($other['mode']) ? '' : $other['mode'];
        $view = empty($other['view']) ? '' : $other['view'];
        $groupby = empty($other['groupby']) ? '' : $other['groupby'];
        $sesscourses = empty($other['sesscourses']) ? '' : $other['sesscourses'];
        $curdate = empty($other['curdate']) ? '' : $other['curdate'];
    }

    if (array_key_exists('send_pseudo', $config) && $config['send_pseudo']) {
        $studentid = sha1(strval($studentid));
    }

    $url = $config['app_url'] . '/mod/attendance/view.php?id=' . $cmid;

    if ($other['studentid'] != '') {
        $url = $url . '&studentid=' . $studentid;
    }
    if ($other['mode'] != '') {
        $url = $url . '&mode=' . $mode;
    }
    if ($other['view'] != '') {
        $url = $url . '&view=' . $view;
    }
    if ($other['groupby'] != '') {
        $url = $url . '&groupby=' . $groupby;
    }
    if ($other['sesscourses'] != '') {
        $url = $url . '&sesscourses=' . $sesscourses;
    }
    if ($other['curdate'] != '') {
        $url = $url . '&curdate=' . $curdate;
    }

    try {
        $repo = $config['repo'];
        $coursemodule = $repo->read_record_by_id('course_modules', $cmid);
        $status = $coursemodule->deletioninprogress;
        if ($status == 0) {
            $description = 'the attendance session report';
        } else {
            $description = 'deletion in progress';
        }
    } catch (Exception $e) {
        // OBJECT_NOT_FOUND.
        $description = 'deleted';
    }

    return [
        'id' => $url,
        'definition' => [
            'type' => 'http://activitystrea.ms/schema/1.0/review',
            'name' => [
                $lang => 'session report',
            ],
            'description' => [
                $lang => $description,
            ],
        ],
    ];
}

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
 * Transformer utility for retrieving assignment status data.
 *
 * @package   logstore_xapi
 * @copyright 2023 Daniela Rotelli <danielle.rotelli@gmail.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace src\transformer\utils\get_activity;

use Exception;

/**
 * Transformer utility for retrieving assignment status data.
 *
 * @param array $config The transformer config settings.
 * @param int $cmid The course module id.
 * @param int $assignid The id of the assignment.
 * @param \stdClass $user The object user.
 * @param string $lang The language of the course.
 * @return array
 */

function assignment_status(array $config, int $cmid, int $assignid, \stdClass $user, string $lang): array {

    try {
        $repo = $config['repo'];
        $assignment = $repo->read_record_by_id('assign', $assignid);
        $submissionstatus = $repo->read_record('assign_submission', ['assignment' => $assignid, 'userid' => $user->id]);
        $name = 'submission status of ' . $assignment->name . ': '. $submissionstatus->status;
        $coursemodule = $repo->read_record_by_id('course_modules', $cmid);
        $status = $coursemodule->deletioninprogress;
        if ($status == 0) {
            $description = 'the status of the assignment submission';
        } else {
            $description = 'deletion in progress';
        }

    } catch (Exception $e) {
        // OBJECT_NOT_FOUND.
        $name = 'submission status of assignment id ' . $assignid;
        $description = 'deleted ';
    }

    return [
        'id' => $config['app_url'] . '/mod/assign/view.php?id=' . $cmid,
        'definition' => [
            'type' => 'http://id.tincanapi.com/activitytype/status-update',
            'name' => [
                $lang => $name,
            ],
            'description' => [
                $lang => $description,
            ],
        ],
    ];
}

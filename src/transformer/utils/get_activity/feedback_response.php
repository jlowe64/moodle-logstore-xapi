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
 * Transformer utility for retrieving feedback response data.
 *
 * @package   logstore_xapi
 * @copyright 2023 Daniela Rotelli <danielle.rotelli@gmail.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace src\transformer\utils\get_activity;

use Exception;

/**
 * Transformer utility for retrieving feedback response data.
 *
 * @param array $config The transformer config settings.
 * @param int $responseid The id of the response.
 * @param int $cmid The id of the course module.
 * @param int $anonymous The anonymous field in the event.
 * @param \stdClass $user The user object.
 * @param string $lang The language of the course.
 * @return array
 */
function feedback_response(array $config, int $responseid, int $cmid, int $anonymous, \stdClass $user, string $lang): array {

    if (array_key_exists('send_pseudo', $config) && $config['send_pseudo']) {
        $userid = sha1($user->id);
    } else {
        $userid = $user->id;
    }

    try {
        $repo = $config['repo'];
        $coursemodule = $repo->read_record_by_id('course_modules', $cmid);
        $status = $coursemodule->deletioninprogress;
        if ($status == 0) {
            $description = 'the attempt of the quiz';
        } else {
            $description = 'deletion in progress';
        }
    } catch (Exception $e) {
        // OBJECT_NOT_FOUND.
        $description = 'deleted';
    }

    if ($anonymous == 1) {
        $url = $config['app_url'].'/mod/feedback/show_entries.php=id'.$cmid.'&showcompleted='.$responseid;
    } else {
        $url = $config['app_url'].'/mod/feedback/show_entries.php=id'.$cmid.'&userid='.$userid.'&showcompleted='.$responseid;
    }

    return [
        'id' => $url,
        'definition' => [
            'type' => 'http://adlnet.gov/expapi/activities/attempt',
            'name' => [
                $lang => 'attempt',
            ],
            'description' => [
                $lang => $description,
            ],
        ],
    ];
}

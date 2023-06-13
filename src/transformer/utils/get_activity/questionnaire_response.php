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
 * Transformer utility for retrieving questionnaire response data.
 *
 * @package   logstore_xapi
 * @copyright 2023 Daniela Rotelli <danielle.rotelli@gmail.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace src\transformer\utils\get_activity;

use Exception;

/**
 * Transformer utility for retrieving questionnaire response data.
 *
 * @param array $config The transformer config settings.
 * @param int $instance The questionnaire id.
 * @param \stdClass $user The user object.
 * @param string $other The field other of the event.
 * @param string $lang The language of the course.
 * @param int $cmid The course module id.
 * @return array
 */

function questionnaire_response(array $config, int $instance, \stdClass $user, string $other, string $lang, int $cmid): array {

    if (array_key_exists('send_pseudo', $config) && $config['send_pseudo']) {
        $userid = sha1(strval($user->id));
    } else {
        $userid = $user->id;
    }

    $other = unserialize($other);
    if (!$other) {
        $rid = empty($other->rid) ? '' : $other->rid;
        $currentgroupid = empty($other->currentgroupid) ? '' : $other->currentgroupid;

    } else {
        $rid = empty($other['rid']) ? '' : $other['rid'];
        $currentgroupid = empty($other['currentgroupid']) ? '' : $other['currentgroupid'];

    }

    $individualresponse = 1;
    $byresponse = 1;

    $url = $config['app_url']
        . '/mod/questionnaire/myreport.php?instance=' . $instance
        . '&user=' . $userid
        . '&action=' . $other['action']
        . '&byresponse=' . $byresponse
        . '&individualresponse=' . $individualresponse
        . '&rid=' . $rid
        . '&group=' . $currentgroupid;

    try {
        $repo = $config['repo'];
        $coursemodule = $repo->read_record_by_id('course_modules', $cmid);
        $status = $coursemodule->deletioninprogress;
        if ($status == 0) {
            $description = 'the response of the questionnaire';
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
                $lang => 'Response report',
            ],
            'description' => [
                $lang => $description,
            ],
        ],
    ];
}

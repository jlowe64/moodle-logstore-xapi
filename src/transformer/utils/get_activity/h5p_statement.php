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
 * Transformer utility for retrieving h5p statement data.
 *
 * @package   logstore_xapi
 * @copyright 2023 Daniela Rotelli <danielle.rotelli@gmail.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace src\transformer\utils\get_activity;

use Exception;

/**
 * Transformer utility for retrieving h5p statement data.
 *
 * @param array $config The transformer config settings.
 * @param string $lang The language of the course.
 * @param int $activityid The id of the h5p activity.
 * @param int $cmid The course module id.
 * @param \stdClass $user The user object.
 * @return array
 */
function h5p_statement(array $config, string $lang, int $activityid, \stdClass $user, int $cmid): array {

    if (array_key_exists('send_pseudo', $config) && $config['send_pseudo']) {
        $userid = sha1(strval($user->id));
    } else {
        $userid = $user->id;
    }

    try {
        $repo = $config['repo'];
        $activity = $repo->read_record_by_id('h5pactivity', $activityid);
        $name = property_exists($activity, 'name') ? $activity->name : 'H5P Activity';
        $coursemodule = $repo->read_record_by_id('course_modules', $cmid);
        $status = $coursemodule->deletioninprogress;
        if ($status == 0) {
            $description = 'the statement of the h5p activity';
        } else {
            $description = 'deletion in progress';
        }
    } catch (Exception $e) {
        // OBJECT_NOT_FOUND.
        $name = 'activity id ' . $activityid;
        $description = 'deleted';
    }

    $url = $config['app_url'] . '/mod/h5pactivity/grade.php?id=' . $cmid . '&user=' . $userid;

    return [
        'id' => $url,
        'definition' => [
            'type' => 'http://www.tincanapi.co.uk/activitytypes/grade_classification',
            'interactionType' => 'fill-in',
            'name' => [
                $lang => 'statement for ' . $name,
            ],
            'description' => [
                $lang => $description,
            ],
        ],
    ];
}

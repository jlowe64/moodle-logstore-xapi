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
 * Transformer utility for retrieving (course discussion) activities.
 *
 * @package   logstore_xapi
 * @copyright Jerret Fowler <jerrett.fowler@gmail.com>
 *            Ryan Smith <https://www.linkedin.com/in/ryan-smith-uk/>
 *            David Pesce <david.pesce@exputo.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace src\transformer\utils\get_activity;

use Exception;
use src\transformer\utils as utils;

/**
 * Transformer utility for retrieving (course discussion) activities.
 *
 * @param array $config The transformer config settings.
 * @param \stdClass $course The course object.
 * @param int $discussionid The id of the discussion.
 * @param int $cmid The course module id.
 * @return array
 */
function course_discussion(array $config, \stdClass $course, int $discussionid, int $cmid): array {

    try {
        $repo = $config['repo'];
        $discussion = $repo->read_record_by_id('forum_discussions', $discussionid);
        $discussionname = property_exists($discussion, 'name') ? $discussion->name : 'Discussion';
        $coursemodule = $repo->read_record_by_id('course_modules', $cmid);
        $status = $coursemodule->deletioninprogress;
        if ($status == 0) {
            $description = 'the forum discussion';
        } else {
            $description = 'deletion in progress';
        }

    } catch (Exception $e) {
        // OBJECT_NOT_FOUND.
        $discussionname = 'discussion id ' . $discussionid;
        $description = 'deleted';
    }

    $courselang = utils\get_course_lang($course);
    $discussionurl = $config['app_url'].'/mod/forum/discuss.php?d='.$discussionid;

    return [
        'id' => $discussionurl,
        'definition' => [
            'type' => 'http://id.tincanapi.com/activitytype/discussion',
            'name' => [
                $courselang => $discussionname,
            ],
            'description' => [
                $courselang => $description,
            ],
        ],
    ];
}

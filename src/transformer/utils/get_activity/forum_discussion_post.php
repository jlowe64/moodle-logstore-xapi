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
 * Transformer utility for retrieving (forum discussion post) activities.
 *
 * @package   logstore_xapi
 * @copyright Jerret Fowler <jerrett.fowler@gmail.com>
 *            Ryan Smith <https://www.linkedin.com/in/ryan-smith-uk/>
 *            David Pesce <david.pesce@exputo.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace src\transformer\utils\get_activity;

use Exception;

/**
 * Transformer utility for retrieving (forum discussion post) activities.
 *
 * @param array $config The transformer config settings.
 * @param int $discussionid The id of the discussion.
 * @param int $postid The id of the post.
 * @param int $cmid The course module id.
 * @param string $lang The language of the course.
 * @return array
 */

function forum_discussion_post(array $config, int $discussionid, int $postid, int $cmid, string $lang): array {

    try {
        $repo = $config['repo'];
        $coursemodule = $repo->read_record_by_id('course_modules', $cmid);
        $status = $coursemodule->deletioninprogress;
        if ($status == 0) {
            $description = 'the forum discussion post';
        } else {
            $description = 'deletion in progress';
        }
    } catch (Exception $e) {
        // OBJECT_NOT_FOUND.
        $description = 'deleted';
    }

    $posturl = $config['app_url'].'/mod/forum/discuss.php?d='.$discussionid.'#p'.$postid;

    return [
        'id' => $posturl,
        'definition' => [
            'type' => 'http://id.tincanapi.com/activitytype/forum-reply',
            'description' => [
                $lang => $description,
            ],
        ],
    ];
}

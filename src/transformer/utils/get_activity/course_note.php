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
 * Transformer utility for cleaning HTML from strings.
 *
 * @package   logstore_xapi
 * @copyright Daniel Bell <daniel@yetanalytics.com>
 *            Milt Reder <milt@yetanalytics.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace src\transformer\utils\get_activity;
use src\transformer\utils as utils;

/**
 * Transformer utility for generating note object for note_created and note_updated events
 *
 * @param array $config
 * @param string $lang
 * @param array $subject
 * @param array $note
 * @return object
 */
function course_note($config, $lang, $subject, $note) {
    return [
        ...base(),
        'id' => $config['app_url'].'/notes/view.php?id='.$note->id,
        'definition' => [
            'name' => [$lang => utils\get_string_html_removed($note->subject)],
            'type' =>  'http://activitystrea.ms/note',
            'description' => [$lang => utils\get_string_html_removed($note->content)],
            'extensions' => [
                "https://xapi.edlm/profiles/edlm-lms/concepts/activity-extensions/note-type" => "course",
                "https://xapi.edlm/profiles/edlm-lms/concepts/activity-extensions/note-subject" =>
                    utils\get_user($config,$subject)
            ]
        ]
    ];
}

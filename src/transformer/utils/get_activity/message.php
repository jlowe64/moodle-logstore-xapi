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
 * Transformer utility for retrieving message data.
 *
 * @package   logstore_xapi
 * @copyright 2023 Daniela Rotelli <danielle.rotelli@gmail.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

namespace src\transformer\utils\get_activity;

use Exception;

/**
 * Transformer utility for retrieving message data.
 *
 * @param array $config The transformer config settings.
 * @param string $lang The language of the course or site.
 * @param int|null $messageid The id of the message.
 * @param int|null $cmid The course module id.
 * @return array
 */

function message(array $config, string $lang, int $messageid=null, int $cmid=null): array {

    if (is_null($messageid)) {
        $url = $config['app_url'].'/message/index.php';
        $description = 'the message of the site messaging system';
    } else {
        try {
            $repo = $config['repo'];
            $chatmessage = $repo->read_record_by_id('chat_messages', $messageid);
            $chatid = $chatmessage->chatid;
            $coursemodule = $repo->read_record_by_id('course_modules', $cmid);
            $status = $coursemodule->deletioninprogress;
            if ($status == 0) {
                $description = 'the message of the chat';
            } else {
                $description = 'deletion in progress';
            }
        } catch (Exception $e) {
            // OBJECT_NOT_FOUND.
            $chatid = 0;
            $description = 'deleted';
        }
        global $CFG;
        $url = $config['app_url'].'/mod/chat/gui_'.$CFG->chat_method.'/index.php?id='.$chatid;
    }

    return [
        'id' => $url,
        'definition' => [
            'type' => 'http://id.tincanapi.com/activitytype/chat-message',
            'name' => [
                $lang => 'Message',
            ],
            'description' => [
                $lang => $description,
            ],
        ],
    ];
}

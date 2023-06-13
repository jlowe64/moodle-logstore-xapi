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
 * Transformer utility for retrieving notification data.
 *
 * @package   logstore_xapi
 * @copyright 2023 Daniela Rotelli <danielle.rotelli@gmail.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace src\transformer\utils\get_activity;

use Exception;

/**
 * Transformer utility for retrieving notification data.
 *
 * @param array $config The transformer config settings.
 * @param int $notificationid The if of the notification.
 * @param string $lang The language of the site.
 * @return array
 */

function notification(array $config, int $notificationid, string $lang): array {

    try {
        $repo = $config['repo'];
        $notification = $repo->read_record_by_id('notifications', $notificationid);
        $name = property_exists($notification, 'subject') ? $notification->subject : 'Notification';

    } catch (Exception $e) {
        // OBJECT_NOT_FOUND.
        $name = 'notification id ' . $notificationid;
    }

    $url = $config['app_url'] . '/message/output/popup/notifications.php?notificationid=' . $notificationid;

    return [
        'id' => $url,
        'definition' => [
            'type' => 'http://activitystrea.ms/schema/1.0/alert',
            'name' => [
                $lang => $name,
            ],
            'description' => [
                $lang => 'the notification of an activity',
            ],
        ],
    ];
}

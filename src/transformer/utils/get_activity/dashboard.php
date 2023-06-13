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
 * Transformer utility for retrieving dashboard data.
 *
 * @package   logstore_xapi
 * @copyright 2023 Daniela Rotelli <danielle.rotelli@gmail.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace src\transformer\utils\get_activity;

/**
 * Transformer utility for retrieving dashboard data.
 *
 * @param array $config The transformer config settings.
 * @param \stdClass $user The user object.
 * @param string $lang The language of the site.
 * @return array
 */

function dashboard(array $config, \stdClass $user, string $lang): array {

    if (array_key_exists('send_pseudo', $config) && $config['send_pseudo']) {
        $userid = sha1(strval($user->id));
    } else {
        $userid = $user->id;
    }

    $url = $config['app_url'] . '/my/?user=' . $userid;

    return [
        'id' => $url,
        'definition' => [
            'type' => 'http://activitystrea.ms/schema/1.0/page',
            'name' => [
                $lang => 'Dashboard',
            ],
            'description' => [
                $lang => 'customisable page for providing users with details of their progress and upcoming deadlines',
            ],
        ],
    ];
}

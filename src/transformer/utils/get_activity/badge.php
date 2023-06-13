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
 * Transformer utility for retrieving badge data.
 *
 * @package   logstore_xapi
 * @copyright 2023 Daniela Rotelli <danielle.rotelli@gmail.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace src\transformer\utils\get_activity;

use Exception;

/**
 * Transformer utility for retrieving badge data.
 *
 * @param array $config The transformer config settings.
 * @param int $badgeid The id of the badge.
 * @param int $badgehash The hash of the badge.
 * @param string $lang The language of the course.
 * @return array
 */

function badge(array $config, int $badgeid, int $badgehash, string $lang): array {

    try {
        $repo = $config['repo'];
        $badge = $repo->read_record_by_id('badge', $badgeid);
        $name = property_exists($badge, 'name') ? $badge->name : 'Badge';
        $description = 'the badge awarded to celebrate achievements';
    } catch (Exception $e) {
        // OBJECT_NOT_FOUND.
        $name = 'badge id ' . $badgeid;
        $description = 'deleted ';
    }

    $url = $config['app_url'] . '/badges/badge.php?hash=' . $badgehash;

    return [
        'id' => $url,
        'definition' => [
            'type' => 'http://activitystrea.ms/schema/1.0/badge',
            'name' => [
                $lang => $name,
            ],
            'description' => [
                $lang => $description,
            ],
        ],
    ];
}

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
 * Transformer utility for retrieving tour data.
 *
 * @package   logstore_xapi
 * @copyright 2023 Daniela Rotelli <danielle.rotelli@gmail.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

namespace src\transformer\utils\get_activity;

use Exception;

/**
 * Transformer utility for retrieving tour data.
 *
 * @param array $config The transformer config settings.
 * @param int $tourid The id of the tour.
 * @param string $lang The language of the tour.
 * @return array
 */

function tour(array $config, int $tourid, string $lang): array {

    try {
        $repo = $config['repo'];
        $tour = $repo->read_record_by_id('tool_usertours_tours', $tourid);
        $name = property_exists($tour, 'name') ? $tour->name : 'Tour';
        $description = 'step-by-step guide to various areas of Moodle';

    } catch (Exception $e) {
        // OBJECT_NOT_FOUND.
        $name = 'tour id ' . $tourid;
        $description = 'deleted';
    }

    $url = $config['app_url'] . '/admin/tool/usertours/configure.php?id=' . $tourid . '&action=viewtour';

    return [
        'id' => $url,
        'definition' => [
            'type' => 'http://adlnet.gov/expapi/activities/media',
            'name' => [
                $lang => 'tour ' . $name
            ],
            'description' => [
                $lang => $description,
            ],
        ],
    ];
}

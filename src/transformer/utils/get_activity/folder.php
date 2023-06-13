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
 * Transformer utility for retrieving folder data.
 *
 * @package   logstore_xapi
 * @copyright 2023 Daniela Rotelli <danielle.rotelli@gmail.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace src\transformer\utils\get_activity;

use Exception;

/**
 * Transformer utility for retrieving folder data.
 *
 * @param array $config The transformer config settings.
 * @param string $lang The language of the course.
 * @param int $folderid The id of the folder.
 * @param int $cmid The course module id.
 * @return array
 */

function folder(array $config, string $lang, int $folderid, int $cmid): array {

    try {
        $repo = $config['repo'];
        $folder = $repo->read_record_by_id('folder', $folderid);
        $name = property_exists($folder, 'name') ? $folder->name : 'Folder';
        $coursemodule = $repo->read_record_by_id('course_modules', $cmid);
        $status = $coursemodule->deletioninprogress;
        if ($status == 0) {
            $description = 'the folder resource';
        } else {
            $description = 'deletion in progress';
        }
    } catch (Exception $e) {
        // OBJECT_NOT_FOUND.
        $name = 'folder id ' . $folderid;
        $description = 'deleted';
    }

    $url = $config['app_url'] . '/mod/folder/view.php?id=' . $cmid;

    return [
        'id' => $url,
        'definition' => [
            'type' => 'http://activitystrea.ms/schema/1.0/collection',
            'name' => [
                $lang => $name,
            ],
            'description' => [
                $lang => $description,
            ],
        ],
    ];
}

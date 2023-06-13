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
 * Transformer utility for retrieving chapter data.
 *
 * @package   logstore_xapi
 * @copyright 2023 Daniela Rotelli <danielle.rotelli@gmail.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace src\transformer\utils\get_activity;

use Exception;

/**
 * Transformer utility for retrieving chapter data.
 *
 * @param array $config The transformer config settings.
 * @param int $chapterid The id of the chapter.
 * @param int $cmid The course module id.
 * @param string $lang The language of the course.
 * @return array
 */

function chapter(array $config, int $chapterid, int $cmid, string $lang): array {

    try {
        $repo = $config['repo'];
        $chapter = $repo->read_record_by_id('book_chapters', $chapterid);
        $title = property_exists($chapter, 'title') ? $chapter->title : 'Chapter';
        $coursemodule = $repo->read_record_by_id('course_modules', $cmid);
        $status = $coursemodule->deletioninprogress;
        if ($status == 0) {
            $description = 'the chapter of the book';
        } else {
            $description = 'deletion in progress';
        }
    } catch (Exception $e) {
        // OBJECT_NOT_FOUND.
        $title = 'chapter id ' . $chapterid;
        $description = 'deleted';
    }

    $url = $config['app_url'] . '/mod/book/tool/print/index.php?id=' . $cmid . '&chapterid=' . $chapterid;

    return [
        'id' => $url,
        'definition' => [
            'type' => 'http://id.tincanapi.com/activitytype/chapter',
            'name' => [
                $lang => 'chapter ' . $title,
            ],
            'description' => [
                $lang => $description,
            ],
        ],
    ];
}

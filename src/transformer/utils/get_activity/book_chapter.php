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
 * Transformer utility for retrieving (book chapter) activities.
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
 * Transformer utility for retrieving the book chapter.
 *
 * @param array $config The transformer config settings.
 * @param \stdClass $course The course object.
 * @param int $chapterid The id of the chapter.
 * @param int $cmid The id of the context.
 * @return array
 */

function book_chapter(array $config, \stdClass $course, int $chapterid, int $cmid): array {

    $courselang = utils\get_course_lang($course);
    $url = $config['app_url'].'/mod/book/view.php?id=' . $cmid . '&chapterid=' . $chapterid;
    $definition = [
        'type' => 'http://id.tincanapi.com/activitytype/chapter'
    ];

    try {
        $repo = $config['repo'];
        $chapter = $repo->read_record_by_id('book_chapters', $chapterid);

        if (property_exists($chapter, 'title')) {
            $definition['name'] = [];
            $definition['name'][$courselang] = $chapter->title;
        }

        if (property_exists($chapter, 'content')) {
            $content = utils\get_string_html_removed($chapter->content);
            $definition['description'] = [
                $courselang => $content
            ];
        }
        $coursemodule = $repo->read_record_by_id('course_modules', $cmid);
        $status = $coursemodule->deletioninprogress;
        if ($status == 1) {
            $definition['description'] = [
                $courselang => 'deletion in progress'
            ];
        }
    } catch (Exception $e) {
        // OBJECT_NOT_FOUND.
        $definition['name'][$courselang] = 'chapter id ' . $chapterid;
        $definition['description'][$courselang] = 'deleted';
    }

    return [
        'id' => $url,
        'definition' => $definition
    ];
}

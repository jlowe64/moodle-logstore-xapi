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
 * Transformer utility for retrieving lesson content page data.
 *
 * @package   logstore_xapi
 * @copyright 2023 Daniela Rotelli <danielle.rotelli@gmail.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace src\transformer\utils\get_activity;

use Exception;

/**
 * Transformer utility for retrieving lesson content page data.
 *
 * @param array $config The transformer config settings.
 * @param string $lang The language of the course.
 * @param int $cmid The course module id.
 * @param int $pageid The id of the page.
 * @param string $target The type of content page.
 * @return array
 */

function lesson_page(array $config, string $lang, int $cmid, int $pageid, string $target): array {

    try {
        $repo = $config['repo'];
        $page = $repo->read_record_by_id('lesson_pages', $pageid);
        $coursemodule = $repo->read_record_by_id('course_modules', $cmid);
        $status = $coursemodule->deletioninprogress;
        if ($status == 0) {
            $description = 'the page of the lesson activity';
        } else {
            $description = 'deletion in progress';
        }

        if ($target == 'question') {
            $name = 'Question: ' . property_exists($page, 'title') ? $page->title : 'Question';
            $type = 'http://adlnet.gov/expapi/activities/question';
        } else {
            $name = 'Content page: ' . property_exists($page, 'title') ? $page->title : 'Content page';
            $type = 'http://activitystrea.ms/schema/1.0/page';
        }

    } catch (Exception $e) {
        // OBJECT_NOT_FOUND.
        $name = 'page id ' . $pageid;
        $description = 'deleted';
    }

    $url = $config['app_url'] . '/mod/lesson/view.php?id=' . $cmid . '&pageid=' . $pageid;

    return [
        'id' => $url,
        'definition' => [
            'type' => $type,
            'name' => [
                $lang => $name,
            ],
            'description' => [
                $lang => $description,
            ],
        ],
    ];
}

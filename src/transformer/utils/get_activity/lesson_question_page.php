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
 * Transformer utility for retrieving (lesson question page) activities.
 *
 * @package   logstore_xapi
 * @copyright Cliff Casey <cliff@yetanalytics.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace src\transformer\utils\get_activity;

use src\transformer\utils as utils;

/**
 * Transformer utility for retrieving (lesson question page) activities.
 *
 * @param array $config The transformer config settings.
 * @param \stdClass $course The course object.
 * @param \stdClass $lesson The lesson object.
 * @param \stdClass $page The lesson page object.
 * @param int $cmid course module id
 * @return array
 */
function lesson_question_page(array $config, \stdClass $course, \stdClass $lesson, \stdClass $page, int $cmid) {
    $repo = $config['repo'];
    $courselang = utils\get_course_lang($course);

    $entryurl = $config['app_url'].'/mod/lesson/view.php?id='.$cmid.'&pageid='.$page->id;

    $activity = [
        ...base(),
        'id' => $entryurl,
    ];

    $answers = $repo->read_records('lesson_answers', ['pageid' => $page->id]);
    $correct_answers = array_filter($answers, function($a){return ($a->score > 0);});

    switch ($page->qtype) {
    case LESSON_PAGE_SHORTANSWER:
        $correct_responses = array_values(
            array_map(
                function($answer) {
                    return utils\get_string_html_removed($answer->response);
                },
                $correct_answers
            )
        );
        $activity['definition'] = utils\get_activity\definition\cmi\fill_in(
            $config,
            $page->title,
            utils\get_string_html_removed($page->contents),
            $courselang,
            $correct_responses
        );
        break;
    case LESSON_PAGE_ESSAY:
        $activity['definition'] = utils\get_activity\definition\cmi\long_fill_in(
            $config,
            $page->title,
            utils\get_string_html_removed($page->contents),
            $courselang
        );
        break;
    case LESSON_PAGE_TRUEFALSE:
    case LESSON_PAGE_MULTICHOICE:
        $choices = array_values(
            array_map(
                function($answer) {
                    return utils\get_string_html_removed($answer->response);
                },
                $answers
            )
        );
        $correct_choices = array_values(
            array_map(
                function($answer) {
                    return utils\get_string_html_removed($answer->response);
                },
                $correct_answers
            )
        );
        $activity['definition'] = utils\get_activity\definition\cmi\choice(
            $config,
            $page->title,
            utils\get_string_html_removed($page->contents),
            $choices,
            $courselang,
            $correct_choices
        );
        break;
    case LESSON_PAGE_MATCHING:
        $source = [];
        $target = [];
        foreach ($answers as $a) {
            if (!empty($a->answer)){
                $source[] = utils\get_string_html_removed($a->answer);
                $target[] = utils\get_string_html_removed($a->response);
            }
        }
        $activity['definition'] = utils\get_activity\definition\cmi\matching(
            $config,
            $page->title,
            utils\get_string_html_removed($page->contents),
            $source,
            $target,
            $courselang
        );
        break;
    case LESSON_PAGE_NUMERICAL:
        // xAPI Numerical can only have one discrete correct response, or a
        // range but lessons do not support ranges, so taking first correct
        // answer to cover most cases.
        $c_choice = reset($correct_answers);
        $activity['definition'] = utils\get_activity\definition\cmi\numeric(
            $config,
            $page->title,
            utils\get_string_html_removed($page->contents),
            null,
            null,
            $courselang,
            utils\get_string_html_removed($c_choice->response)
        );
        break;
    }
    return $activity;
}

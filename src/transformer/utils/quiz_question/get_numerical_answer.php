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
 * Transformer utility for retrieving quiz question numeric answers.
 *
 * @package   logstore_xapi
 * @copyright Milt Reder <milt@yetanalytics.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace src\transformer\utils\quiz_question;

use src\transformer\utils as utils;

/**
 * Transformer utility for retrieving quiz question numeric answers.
 *
 * @param array $config The transformer config settings.
 * @param int $questionid
 * @return array
 */
function get_numerical_answer(
    array $config,
    int $questionid
) {
    $repo = $config['repo'];
    $answers = $repo->read_records('question_answers', [
        'question' => $questionid
    ]);
    // We only support the answer with the highest fraction
    usort($answers, function ($a, $b) {
        return $b->fraction <=> $a->fraction;
    });
    $answer = reset($answers);
    $answernums = $repo->read_records(
        'question_numerical', [
            'answer' => $answer->id
        ]);
    $answernum = reset($answernums);
    $min = $answer->answer - $answernum->tolerance;
    $max = $answer->answer + $answernum->tolerance;
    $target = $answer->answer;
    return [
        'min' => $min,
        'max' => $max,
        'target' => $target,
    ];

}

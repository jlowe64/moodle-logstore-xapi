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
 * Transformer utility for retrieving (quiz attempt) activities.
 *
 * @package   logstore_xapi
 * @copyright Jerret Fowler <jerrett.fowler@gmail.com>
 *            Ryan Smith <https://www.linkedin.com/in/ryan-smith-uk/>
 *            David Pesce <david.pesce@exputo.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace src\transformer\utils\get_activity;

use Exception;

/**
 * Transformer utility for retrieving (quiz attempt) activities.
 *
 * @param array $config The transformer config settings.
 * @param int $attemptid The id of the attempt.
 * @param int $cmid The id of the course module.
 * @return array
 */
function quiz_attempt(array $config, int $attemptid, int $cmid): array {

    $lang = $config['source_lang'];

    try {
        $repo = $config['repo'];
        $attempt = $repo->read_record_by_id('quiz_attempts', $attemptid);
        $description = 'the attempt ' . $attempt->uniqueid .' of the quiz has been ' . $attempt->state;

    } catch (Exception $e) {
        // OBJECT_NOT_FOUND.
        $description = 'deleted';
    }

    return [
        'id' => $config['app_url'].'/mod/quiz/attempt.php?attempt='.$attemptid.'&cmid='.$cmid,
        'definition' => [
            'type' => 'http://adlnet.gov/expapi/activities/attempt',
            'name' => [
                $lang => 'attempt',
            ],
            'description' => [
                $lang => $description,
            ],
        ],
    ];

}

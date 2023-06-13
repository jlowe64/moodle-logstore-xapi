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
 * @copyright Daniela Rotelli <danielle.rotelli@gmail.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace src\transformer\utils\get_activity;

use Exception;

/**
 * Transformer utility for retrieving (quiz attempt) activities.
 *
 * @param array $config The transformer config settings.
 * @param int $attemptid The id of the attempt.
 * @return array
 */
function quiz_attempt_summary(array $config, int $attemptid): array {

    $lang = $config['source_lang'];

    try {
        $repo = $config['repo'];
        $attempt = $repo->read_record_by_id('quiz_attempts', $attemptid);
        $description = 'summary of the attempt ' . $attemptid . ' of the quiz ' . $attempt->quiz;

    } catch (Exception $e) {
        // OBJECT_NOT_FOUND.
        $description = 'deleted';
    }

    return [
        'id' => $config['app_url'] . '/mod/quiz/summary.php?attempt=' . $attemptid,
        'definition' => [
            'type' => 'http://activitystrea.ms/schema/1.0/review',
            'name' => [
                $lang => 'attempt summary',
            ],
            'description' => [
                $lang => $description,
            ],
        ],
    ];
}

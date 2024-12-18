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
 * Transformer utility for creating the default statement context.
 *
 * @package   logstore_xapi
 * @copyright Milt Reder <milt@yetanalytics.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace src\transformer\utils;

/**
 * Transformer utility for creating the default statement context.
 *
 * @param array $config The transformer config.
 * @param \stdClass $event The moodle event.
 * @param string $lang The language.
 * @param ?\stdClass $course The course.
 * @return array
 */
function get_context_base(
    array $config,
    \stdClass $event,
    string $lang,
    ?\stdClass $course = null
) {
    return [
        'language' => $lang,
        'extensions' => extensions\base($config, $event, $course),
    ];
}

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
 * Utility for converting nested objects to arrays.
 *
 * @package   logstore_xapi
 * @copyright Milt Reder <milt@yetanalytics.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace TestUtils;

/**
 * Given a deeply nested object representing JSON, make it an array
 *
 * @param \stdClass $data the object
 * @return array
 */
function objectToArray($data) {
    // If the data is an object, convert it into an array
    if ($data instanceof \stdClass) {
        $data = (array)$data;
    }

    // If the data is an array, apply the function recursively to each element
    if (is_array($data)) {
        foreach ($data as $key => $value) {
            $data[$key] = objectToArray($value); // Recursive call
        }
    }
    return $data;
}

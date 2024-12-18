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
 * Utility for deep-merging arrays
 *
 * @package   logstore_xapi
 * @copyright Milt Reder <milt@yetanalytics.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace src\transformer\utils;

/**
 * Merge two arrays including deep assignments.
 *
 * @param array $arr1 The first array
 * @param array $arr2 The second array
 * @return array
 */
function deep_merge_arrays($arr1, $arr2) {
    // Merge the second array into the first one
    foreach ($arr2 as $key => $value) {
        // If the key exists in the first array and both values are arrays, recurse
        if (array_key_exists($key, $arr1) && is_array($arr1[$key]) && is_array($value)) {
            $arr1[$key] = deep_merge_arrays($arr1[$key], $value);
        } else {
            // Otherwise, use the second array's value (overwrites or sets new key)
            $arr1[$key] = $value;
        }
    }
    return $arr1;
}

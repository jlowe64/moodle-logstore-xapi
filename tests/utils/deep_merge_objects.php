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
 * Utility for deep-merging objects
 *
 * @package   logstore_xapi
 * @copyright Milt Reder <milt@yetanalytics.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace TestUtils;

/**
 * Merge two objects including deep assignments.
 *
 * @param \stdClass $obj1 The first object
 * @param \stdClass $obj2 The second object
 * @return \stdClass
 */
function deep_merge_objects($obj1, $obj2) {
    $newObject = clone $obj1; // Clone the first object

    foreach ($obj2 as $property => $value) {
        // Check if both properties are objects and merge recursively
        if (isset($newObject->$property) && is_object($newObject->$property) && is_object($value)) {
            $newObject->$property = deep_merge_objects($newObject->$property, $value);
        } else {
            // Otherwise, overwrite the property
            $newObject->$property = $value;
        }
    }
    return $newObject;
}

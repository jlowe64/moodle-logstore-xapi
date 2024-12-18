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
 * Utility to make human-readable but id-safe strings.
 *
 * @package   logstore_xapi
 * @copyright Milt Reder <milt@yetanalytics.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace src\transformer\utils;

/**
 * Transformer utility that converts a given string into a URL-friendly "slug".
 *
 * @param string $string The input string to be converted into a slug.
 * @return string The URL-friendly slug created from the input string.
 */

function slugify($string) {
    // Convert the string to lowercase
    $string = strtolower($string);

    // Replace spaces, underscores and consecutive whitespace with a single dash
    $string = preg_replace('/[\s_]+/', '-', $string);

    // Remove any non-alphanumeric characters except dashes
    $string = preg_replace('/[^a-z0-9-]/', '', $string);

    // Trim any trailing or leading dashes
    $string = trim($string, '-');

    return $string;
}

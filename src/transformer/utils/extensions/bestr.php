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
 * Transformer utility for Bestr xAPI extensions.
 *
 * @package   logstore_xapi
 * @copyright CINECA
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace src\transformer\utils\extensions;

use src\transformer\utils as utils;

/**
 * Transformer utility for base xAPI extensions - Bestr specific.
 *
 * @param array $config The transformer config settings.
 * @param \stdClass $event The event to be transformed.
 * @param object $course The course object.
 * @return array
 */
function bestr(array $config, \stdClass $event, $course) {
    if (utils\is_enabled_config($config, 'send_bestr_data')) {
        $repo = $config['repo'];
        if (isset($event->relateduserid) && $event->relateduserid) {
            $user = $repo->read_record_by_id('user', $event->relateduserid);
        } else {
            $user = $repo->read_record_by_id('user', $event->userid);
        }

        // User not loggedin.
        if (!$user) {
            return [];
        }

        $birthdatefieldname = $config['bestr_custom_birthdate'];
        $cffieldname = $config['bestr_custom_cf'];
        $extrafields = [];
        $prefix = 'profile_field_';

        // If $birthdatefieldname starts with $prefix.
        if ($birthdatefieldname && (substr($birthdatefieldname, 0, strlen($prefix)) == $prefix)) {
            $fieldname = substr($birthdatefieldname, strlen($prefix));
            // Use read_records to avoid exceptions in case the element doesn't exist.
            $tmp = $repo->read_records('user_info_field', ['shortname' => $fieldname]);
            $userextrafield = reset($tmp);  // Take the first (and only) array element.
            if ($userextrafield) {
                // Use read_records to avoid exceptions in case the element doesn't exist.
                $tmp2 = $repo->read_records('user_info_data', ['userid' => $user->id, 'fieldid' => $userextrafield->id]);
                $extra = reset($tmp2);      // Take the first (and only) array element.
                if ($extra && isset($extra->data) && $extra->data) {
                    $extrafields["actor_birthday"] = $extra->data;
                }
            }
        } else {
            if ($birthdatefieldname && isset($user->$birthdatefieldname) && $user->$birthdatefieldname) {
                $extrafields["actor_birthday"] = $user->$birthdatefieldname;
            }
        }

        if ($cffieldname && (substr($cffieldname, 0, strlen($prefix)) == $prefix)) {  // If $cffieldname starts with $prefix.
            $fieldname = substr($cffieldname, strlen($prefix));
            // Use read_records to avoid exceptions in case the element doesn't exist.
            $tmp = $repo->read_records('user_info_field', ['shortname' => $fieldname]);
            $userextrafield = reset($tmp);  // Take the first (and only) array element.
            if ($userextrafield) {
                // Use read_records to avoid exceptions in case the element doesn't exist.
                $tmp2 = $repo->read_records('user_info_data', ['userid' => $user->id, 'fieldid' => $userextrafield->id]);
                $extra = reset($tmp2);      // Take the first (and only) array element.
                if ($extra && isset($extra->data) && $extra->data) {
                    $extrafields["actor_cf"] = $extra->data;
                }
            }
        } else {
            if ($cffieldname && isset($user->$cffieldname) && $user->$cffieldname) {
                $extrafields["actor_cf"] = $user->$cffieldname;
            }
        }

        return [
            'http://lrs.bestr.it/lrs/define/context/extensions/actor' => array_merge([
                    "actor_name" => $user->firstname,
                    "actor_surname" => $user->lastname,
                    ], $extrafields     // If extra fields are present, they are added here.
                ),
            ];
    }
    return [];
}

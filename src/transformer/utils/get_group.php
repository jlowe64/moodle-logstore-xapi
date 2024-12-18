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
 * Transformer utility for retrieving group data.
 *
 * @package   logstore_xapi
 * @copyright Milt Reder <milt@yetanalytics.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace src\transformer\utils;

/**
 * Transformer utility for retrieving group data.
 *
 * @param array $config The transformer config settings.
 * @param \stdClass $group The group object.
 * @return array
 */
function get_group(array $config, \stdClass $group) {
  $repo = $config['repo'];
  $members = $repo->read_records(
    'groups_members',
    ['groupid' => $group->id]
  );
  $users =
    array_values(
      array_map(
        function($member) use ($repo) {
          return $repo->read_record_by_id('user', $member->userid);
        },
        $members
      )
    );

  return [
    'objectType' => 'Group',
    ...((isset($group->name))
        ? [
          'name' => $group->name
        ]
        : []),
    'member' => array_values(
      array_map(
        function($user) use ($config) {
          return get_user($config, $user);
        },
        $users
      )
    ),
  ];
}

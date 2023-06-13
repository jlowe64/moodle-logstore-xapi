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
 * Transformer utility for retrieving role data.
 *
 * @package   logstore_xapi
 * @copyright 2023 Daniela Rotelli <danielle.rotelli@gmail.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

namespace src\transformer\utils;

use Exception;

/**
 * Transformer utility for retrieving role data.
 *
 * @param array $config The transformer config settings.
 * @param int $roleid The role id.
 * @param string $lang The course lang.
 * @return array
 */
function get_role(array $config, int $roleid, string $lang): array {

    try {
        $repo = $config['repo'];
        $role = $repo->read_record_by_id('role', $roleid);
        $name = $role->shortname.' role';
        $description = 'the role assigned to the user in the course';
    } catch (Exception $e) {
        // OBJECT_NOT_FOUND.
        $name = 'assigned role';
        $description = 'deleted';
    }

    $url = $config['app_url'].'/admin/roles/define.php?action=view&roleid='.$roleid;
    $type = 'http://activitystrea.ms/schema/1.0/role';

    return [
        'id' => $url,
        'definition' => [
            'type' => $type,
            'name' => [
                $lang => $name,
            ],
            'description' => [
                $lang => $description,
            ],
        ],
    ];
}

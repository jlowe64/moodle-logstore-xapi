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
 * Transformer utility for retrieving (course module) activities.
 *
 * @package   logstore_xapi
 * @copyright Jerret Fowler <jerrett.fowler@gmail.com>
 *            Ryan Smith <https://www.linkedin.com/in/ryan-smith-uk/>
 *            David Pesce <david.pesce@exputo.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace src\transformer\utils\get_activity;

use Exception;
use src\transformer\utils as utils;

/**
 * Transformer utility for retrieving (course module) activities.
 *
 * @param array $config The transformer config settings.
 * @param \stdClass $course The course object.
 * @param int $cmid The id of the context.
 * @param string $xapitype The type of xAPI object.
 * @return array
 */

function course_module(array $config, \stdClass $course, int $cmid, string $xapitype) {

    $lang = utils\get_course_lang($course);

    try {
        $repo = $config['repo'];
        $coursemodule = $repo->read_record_by_id('course_modules', $cmid);
        $module = $repo->read_record_by_id('modules', $coursemodule->module);
        $instance = $repo->read_record_by_id($module->name, $coursemodule->instance);
        $url = $config['app_url'].'/mod/'.$module->name.'/view.php?id='.$cmid;
        $name = property_exists($instance, 'name') ? $instance->name : $module->name;
        if (is_null($name)) {
            $name = 'Module name';
        }
        $status = $coursemodule->deletioninprogress;
        if ($status == 0) {
            $description = 'the module ' . $module->name . ' of the course';
        } else {
            $description = 'deletion in progress';
        }

        if (utils\is_enabled_config($config, 'send_course_and_module_idnumber')) {
            $moduleidnumber = property_exists($coursemodule, 'idnumber') ? $coursemodule->idnumber : null;
            $lmsexternalid = 'https://w3id.org/learning-analytics/learning-management-system/external-id';
            $object['definition']['extensions'][$lmsexternalid] = $moduleidnumber;
        }
    } catch (Exception $e) {
        // OBJECT_NOT_FOUND.
        $description = 'deleted';
        $url = $config['app_url'].'/mod/';
        $name = 'not available';
    }

    $object = [
        'id' => $url,
        'definition' => [
            'type' => $xapitype,
            'name' => [
                $lang => $name,
            ],
            'description' => [
                $lang => $description,
            ],
        ],
    ];

    return $object;
}

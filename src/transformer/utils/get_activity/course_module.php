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
 *            Milt Reder <milt@yetanalytics.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace src\transformer\utils\get_activity;

use src\transformer\utils as utils;

/**
 * Transformer utility for retrieving (course module) activities.
 *
 * @param array $config The transformer config settings.
 * @param \stdClass $course The course object.
 * @param int $cmid The id of the context.
 * @return array
 */
function course_module(array $config, \stdClass $course, int $cmid) {
    $repo = $config['repo'];
    $coursemodule = $repo->read_record_by_id('course_modules', $cmid);
    $module = $repo->read_record_by_id('modules', $coursemodule->module);
    $instance = $repo->read_record_by_id($module->name, $coursemodule->instance);

    $coursemoduleurl = $config['app_url'].'/mod/'.$module->name.'/view.php?id='.$cmid;
    $courselang = utils\get_course_lang($course);
    $instancename = property_exists($instance, 'name') ? $instance->name : $module->name;

    $activitytype = utils\get_module_activity_type(
        $module->name,
        utils\is_enabled_config($config, 'send_jisc_data')
    );

    // default definition
    $def = [
        'type' => $activitytype,
        'name' => [
            $courselang => $instancename,
        ],
    ];

    // process special cases

    // Choice
    if ($module->name === 'choice') {
        $def = utils\get_activity\definition\choice\get_choice_definition(
            $config, $instance, $courselang
        );
    }

    // Survey & Wiki use "intro"
    if ($module->name === 'survey' || $module->name === 'wiki') {
        $def = [
            'type' => $activitytype,
            'name' => [
                $courselang => $instancename,
            ],
            'description' => [
                $courselang => utils\get_string_html_removed($instance->intro),
            ],
        ];
    }

    $object = [
        ...base(),
        'id' => $coursemoduleurl,
        'definition' => $def,
    ];

    if (utils\is_enabled_config($config, 'send_course_and_module_idnumber')) {
        $moduleidnumber = property_exists($coursemodule, 'idnumber') ? $coursemodule->idnumber : null;
        $lmsexternalid = 'https://w3id.org/learning-analytics/learning-management-system/external-id';
        $object['definition']['extensions'][$lmsexternalid] = $moduleidnumber;
    }

    return $object;
}

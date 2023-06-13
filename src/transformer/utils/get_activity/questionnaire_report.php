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
 * Transformer utility for retrieving questionnaire report data.
 *
 * @package   logstore_xapi
 * @copyright 2023 Daniela Rotelli <danielle.rotelli@gmail.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace src\transformer\utils\get_activity;

use Exception;

/**
 * Transformer utility for retrieving questionnaire report data.
 *
 * @param array $config The transformer config settings.
 * @param string $cmid The id of the course module.
 * @param string $other The field other of the event.
 * @param string $lang The language of the course.
 * @param int $questionnaireid The id of the questionnaire.
 * @return array
 */
function questionnaire_report(array $config, string $cmid, string $other, string $lang, int $questionnaireid): array {

    $other = unserialize($other);
    if (!$other) {
        $action = empty($other->action) ? '' : $other->action;
        $instance = empty($other->instance) ? '' : $other->instance;
        $group = empty($other->group) ? '' : $other->group;
    } else {
        $action = empty($other['action']) ? '' : $other['action'];
        $instance = empty($other['instance']) ? '' : $other['instance'];
        $group = empty($other['group']) ? '' : $other['group'];
    }

    try {
        $repo = $config['repo'];
        $questionnaire = $repo->read_record_by_id('questionnaire', $questionnaireid);
        $name = property_exists($questionnaire, 'name') ? $questionnaire->name : 'Questionnaire';
        $coursemodule = $repo->read_record_by_id('course_modules', $cmid);
        $status = $coursemodule->deletioninprogress;
        if ($status == 0) {
            $description = 'the report of the questionnaire';
        } else {
            $description = 'deletion in progress';
        }

    } catch (Exception $e) {
        // OBJECT_NOT_FOUND.
        $name = 'questionnaire id ' . $questionnaireid;
        $description = 'deleted';
    }

    $url = $config['app_url'].'/mod/questionnaire/report.php?id='.$cmid.'&action='.$action.'&instance='.$instance.'&group='.$group;

    return [
        'id' => $url,
        'definition' => [
            'type' => 'http://activitystrea.ms/schema/1.0/review',
            'name' => [
                $lang => 'report on ' . $name,
            ],
            'description' => [
                $lang => $description,
            ],
        ],
    ];
}

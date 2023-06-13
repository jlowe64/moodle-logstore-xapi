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
 * Transformer utility for retrieving choice data.
 *
 * @package   logstore_xapi
 * @copyright 2023 Daniela Rotelli <danielle.rotelli@gmail.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace src\transformer\utils\get_activity;

use Exception;

/**
 * Transformer utility for retrieving choice data.
 *
 * @param array $config The transformer config settings.
 * @param int $cmid The course module id.
 * @param string $lang The language of the course.
 * @param int|null $choiceid The id of the choice.
 * @param int|null $choicegroupid The id of the choice group.
 * @return array
 */

function choice(array $config, int $cmid, string $lang, int $choiceid=null, int $choicegroupid=null): array {

    $repo = $config['repo'];

    if (is_null($choiceid)) {
        try {
            $choicegroup = $repo->read_record_by_id('choicegroup', $choicegroupid);
            $name = property_exists($choicegroup, 'name') ? $choicegroup->name : 'Choice Group';
        } catch (Exception $e) {
            // OBJECT_NOT_FOUND.
            $name = 'choice group id ' . $choicegroupid;
        }
        $url = $config['app_url'].'/mod/choicegroup/view.php?id=' . $cmid;
    } else {
        try {
            $choice = $repo->read_record_by_id('choice', $choiceid);
            $name = property_exists($choice, 'name') ? $choice->name : 'Choice';
        } catch (Exception $e) {
            // OBJECT_NOT_FOUND.
            $name = 'choice id ' . $choiceid;
        }
        $url = $config['app_url'] . '/mod/choice/view.php?id=' . $cmid;
    }

    try {
        $coursemodule = $repo->read_record_by_id('course_modules', $cmid);
        $status = $coursemodule->deletioninprogress;
        if (is_null($choiceid) && $status == 0) {
            $description = 'the choice group activity';
        } else if (is_null($choicegroupid) && $status == 0) {
            $description = 'the choice activity';
        } else {
            $description = 'deletion in progress';
        }
    } catch (Exception $e) {
        // OBJECT_NOT_FOUND.
        $description = 'deleted';
    }

    return [
        'id' => $url,
        'definition' => [
            'type' => 'http://adlnet.gov/expapi/activities/cmi.interaction',
            'interactionType' => 'choice',
            'name' => [
                $lang => $name,
            ],
            'description' => [
                $lang => $description,
            ],
        ],
    ];
}

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
 * Transformer utility for retrieving (SCORM) activities.
 *
 * @package   logstore_xapi
 * @copyright Jerret Fowler <jerrett.fowler@gmail.com>
 *            Ryan Smith <https://www.linkedin.com/in/ryan-smith-uk/>
 *            David Pesce <david.pesce@exputo.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace src\transformer\utils\get_activity;

use Exception;

/**
 * Transformer utility for retrieving (SCORM) activities.
 *
 * @param array $config The transformer config settings.
 * @param string $cmid The id of the context.
 * @param int $scormid The id of the SCORM.
 * @param string $lang The language of the SCORM activity.
 * @return array
 */
function course_scorm(array $config, string $cmid, int $scormid, string $lang): array {

    try {
        $repo = $config['repo'];
        $scorm = $repo->read_record_by_id('scorm', $scormid);
        $name = property_exists($scorm, 'name') ? $scorm->name : 'Scorm';
        $coursemodule = $repo->read_record_by_id('course_modules', $cmid);
        $status = $coursemodule->deletioninprogress;
        if ($status == 0) {
            $description = 'the scorm activity';
        } else {
            $description = 'deletion in progress';
        }
    } catch (Exception $e) {
        // OBJECT_NOT_FOUND.
        $name = 'scorm id ' . $scormid;
        $description = 'deleted';
    }

    return [
        'id' => $config['app_url'].'/mod/scorm/view.php?id='.$cmid,
        'definition' => [
            'type' => 'http://id.tincanapi.com/activitytype/legacy-learning-standard',
            'name' => [
                $lang => $name,
            ],
            'description' => [
                $lang => $description,
            ],
        ],
    ];
}

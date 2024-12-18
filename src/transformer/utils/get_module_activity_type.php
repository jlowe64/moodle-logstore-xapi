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
 * Transformer utility for retrieving course module activity types.
 *
 * @package   logstore_xapi
 * @copyright Milt Reder <milt@yetanalytics.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace src\transformer\utils;

use src\transformer\utils as utils;

/**
 * Return the mapping of modules to activity types.
 * @param bool $send_jisc whether sending JISC data is enabled.
 * @return array
 */

function get_module_activity_type_mapping(bool $send_jisc) {
    return [
        'assign'          => 'https://xapi.edlm/profiles/edlm-lms/concepts/activity-types/assignment',
        'bigbluebuttonbn' => 'http://adlnet.gov/expapi/activities/meeting',
        'book'            => 'http://id.tincanapi.com/activitytype/book',
        'chat'            => 'http://id.tincanapi.com/activitytype/chat-channel',
        'choice'          => 'http://adlnet.gov/expapi/activities/cmi.interaction',
        'data'            => 'http://xapi.org.au/contentprofile/activitytype/database',
        'facetoface'      => 'https://w3id.org/xapi/acrossx/activities/face-to-face-discussion',
        'feedback'        => 'https://xapi.edlm/profiles/edlm-lms/concepts/activity-types/feedback',
        'folder'          => 'http://activitystrea.ms/collection',
        'forum'           => 'https://xapi.edlm/profiles/edlm-lms/concepts/activity-types/forum',
        'glossary'        => 'https://xapi.edlm/profiles/edlm-lms/concepts/activity-types/glossary',
        'h5pactivity'     => 'http://adlnet.gov/expapi/activities/media',
        'imscp'           => 'https://w3id.org/xapi/cmi5/activitytype/course',
        'label'           => 'https://w3id.org/xapi/acrossx/activities/webpage',
        'lesson'          => 'http://adlnet.gov/expapi/activities/lesson',
        'lti'             => 'http://adlnet.gov/expapi/activities/media',
        'page'            => 'https://w3id.org/xapi/acrossx/activities/page',
        'quiz'            => $send_jisc
            ? 'http://xapi.jisc.ac.uk/activities/quiz'
            : 'http://adlnet.gov/expapi/activities/assessment',
        'resource'        => 'http://id.tincanapi.com/activitytype/resource',
        'scorm'           => 'http://adlnet.gov/expapi/activities/module',
        'url'             => 'http://adlnet.gov/expapi/activities/link',
        'wiki'            => 'https://xapi.edlm/profiles/edlm-lms/concepts/activity-types/wiki',
        'workshop'        => 'https://xapi.edlm/profiles/edlm-lms/concepts/activity-types/workshop',
        'questionnaire'   => 'http://id.tincanapi.com/activitytype/survey',
        'survey'          => 'http://id.tincanapi.com/activitytype/survey'
    ];
}

/**
 * Return an activity type or default for a given module name.
 *
 * @param string $module_name The name of a moodle course module.
 * @param ?bool $send_jisc whether sending JISC data is enabled.
 * @return string
 */
function get_module_activity_type(string $module_name, ?bool $send_jisc = false) {
    $module_uris = get_module_activity_type_mapping($send_jisc);

    if (array_key_exists($module_name, $module_uris)) {
        return $module_uris[$module_name];
    } else {
        return 'http://id.tincanapi.com/activitytype/lms/module';
    }
}

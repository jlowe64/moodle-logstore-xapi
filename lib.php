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

defined('MOODLE_INTERNAL') || die();

define('XAPI_REPORT_ID_ERROR', 0);
define('XAPI_REPORT_ID_HISTORIC', 1);

// Type constants.
define('XAPI_IMPORT_TYPE_LIVE', 0);
define('XAPI_IMPORT_TYPE_HISTORIC', 1);
define('XAPI_IMPORT_TYPE_FAILED', 2);

// Report source.
define('XAPI_REPORT_SOURCE_LOG', 'logstore_xapi_log');
define('XAPI_REPORT_SOURCE_FAILED', 'logstore_xapi_failed_log');
define('XAPI_REPORT_SOURCE_HISTORICAL', 'logstore_standard_log');

// Error types.
define('XAPI_REPORT_ERRORTYPE_NETWORK', 101);
define('XAPI_REPORT_ERRORTYPE_RECIPE', 400);
define('XAPI_REPORT_ERRORTYPE_AUTH', 401);
define('XAPI_REPORT_ERRORTYPE_LRS', 500);
define('XAPI_REPORT_ERRORTYPE_TRANSFORM', 10000); // This high number has been set to avoid conflicting with other error codes

/**
 * Get all visible cohorts in the system.
 *
 * @return array Returns an array of all visible cohorts.
 */
function logstore_xapi_get_cohorts() {
    global $DB;
    $array = array("visible" => 1);
    $cohorts = $DB->get_records("cohort", $array);
    return $cohorts;
}

/**
 * Get the selected cohorts from the settings.
 *
 * @return array Returns an array of selected cohort ids if the cohort is still visible.
 * The cohort might have been made invisible or removed since the selection was made.
 */
function logstore_xapi_get_selected_cohorts() {
    $arrvisible = logstore_xapi_get_cohorts();
    $selected = get_config('logstore_xapi', 'cohorts');

    $arrselected = explode(",", $selected);
    $arr = array();
    foreach ($arrselected as $arrselection) {
        if (array_key_exists($arrselection, $arrvisible)) {
            $arr[] = $arrselection;
        }
    }
    return $arr;
}

/**
 * Return all members for a cohort
 *
 * @param array $cohortids array of cohort ids
 * @return array with cohort id keys containing arrays of user email addresses
 */
function logstore_xapi_get_cohort_members($cohortids) {
    global $DB;

    $members = array();

    foreach ($cohortids as $cohortid) {
        // Validate params.
        $cohort = $DB->get_record('cohort', array('id' => $cohortid), '*', MUST_EXIST);
        if (!empty($cohort)) {
            $sql = "SELECT u.*
                      FROM {user} u, {cohort_members} cm
                     WHERE u.id = cm.userid AND cm.cohortid = ?
                  ORDER BY lastname ASC, firstname ASC";
            $cohortmembers = $DB->get_records_sql($sql, array($cohort->id));
            $members = array_merge($members, $cohortmembers);
        }
    }
    return $members;
}

/**
 * Get the selected cohorts from the settings.
 *
 * @return array Returns an array of user objects from cohorts and additional email addresses.
 */
function logstore_xapi_get_users_for_notifications() {
    // get selected cohort users, will return a blank array if no cohorts are set.
    $cohorts = logstore_xapi_get_selected_cohorts();
    $users = logstore_xapi_get_cohort_members($cohorts);

    // Get the manually set email addresses from the config
    $emailaddresses = get_config('logstore_xapi', 'send_additional_email_addresses');
    $emailaddresses = explode(",", $emailaddresses);
    foreach ($emailaddresses as $email) {
        // Remove whitespace from email addresses
        $email = preg_replace('/\s+/', '', $email);
        if (validate_email($email)) {
            // If the email address is valid then add it to the list of users
            $user = new stdClass();
            $user->email = $email;
            $users[] = $user;
        }
    }

    return $users;
}

/**
 * Gets the unique column values
 *
 * @param $column
 * @return array
 * @throws dml_exception
 */
function logstore_xapi_get_distinct_options_from_failed_table($column) {
    global $DB;

    $options = [0 => get_string('any')];
    $results = $DB->get_fieldset_select('logstore_xapi_failed_log', "DISTINCT $column", '');
    if ($results) {
        foreach ($results as $result) {
            $options[$result] = $result;
        }
    }
    return $options;
}

/**
 * Get the available context's from the logstore standard log table
 *
 * @return array
 * @throws dml_exception
 */
function logstore_xapi_get_logstore_standard_context_options() {
    global $DB;

    $options = [0 => get_string('any')];

    $sql = 'SELECT DISTINCT(contextid)
              FROM {logstore_standard_log} lssl
             WHERE EXISTS (SELECT 1
                             FROM {context} c
                            WHERE c.id = lssl.contextid)';
    $contextids = array_keys($DB->get_records_sql($sql));

    foreach ($contextids as $contextid) {
        $context = context::instance_by_id($contextid);
        $options[$context->id] = $context->get_context_name();
    }

    return $options;
}

/**
 * Retrieves the available and enabled events for this plugin and outputs it into an array
 *
 * @return array
 */
function logstore_xapi_get_event_names_array() {

    if (!function_exists('\src\transformer\get_event_function_map')) {
        global $CFG;

        require_once($CFG->dirroot . '/admin/tool/log/store/xapi/src/transformer/get_event_function_map.php');
    }

    $eventnames = [];
    $eventfunctionmap = \src\transformer\get_event_function_map();
    foreach (array_keys($eventfunctionmap) as $eventname) {
        $eventnames[$eventname] = $eventname;
    }
    return $eventnames;
}

/**
 * Decode the json array stored in the response column. Will return false if json is invalid
 *
 * @param $response
 * @return array|bool
 */
function logstore_xapi_decode_response($response) {
    $decode = json_decode($response, true);
    // Check JSON is valid
    if (json_last_error() === JSON_ERROR_NONE) {
        return $decode;
    }
    return false;
}

/**
 * Generate the string for the info column in the report
 *
 * @param $row
 * @return string
 * @throws coding_exception
 */
function logstore_xapi_get_info_string($row) {
    if (!empty($row->errortype)) {
        switch ($row->errortype) {
            case XAPI_REPORT_ERRORTYPE_NETWORK:
                return get_string('networkerror', 'logstore_xapi');
            case XAPI_REPORT_ERRORTYPE_RECIPE:
                // Recipe issue
                return get_string('recipeerror', 'logstore_xapi');
            case XAPI_REPORT_ERRORTYPE_AUTH:
                // Unauthorised, could be an issue with xAPI credentials
                return get_string('autherror', 'logstore_xapi');
            case XAPI_REPORT_ERRORTYPE_LRS:
                // xAPI server error
                return get_string('lrserror', 'logstore_xapi');
            case XAPI_REPORT_ERRORTYPE_TRANSFORM:
                // Transform error
                return get_string('failedtransformresponse', 'logstore_xapi', $row->eventname);
            default:
                // Generic error catch all
                return get_string('unknownerror', 'logstore_xapi', $row->errortype);
                break;
        }
    }
    return ''; // Return blank if no errortype captured
}

/**
 * determine the type from the initial base table
 *
 * @param string $table
 * @return int
 */
function logstore_xapi_get_type_from_table($table) {
    switch ($table) {
        case XAPI_REPORT_SOURCE_LOG:
            return XAPI_IMPORT_TYPE_LIVE;
        case XAPI_REPORT_SOURCE_FAILED:
            return XAPI_IMPORT_TYPE_FAILED;
        case XAPI_REPORT_SOURCE_HISTORICAL:
            return XAPI_IMPORT_TYPE_HISTORIC;
        default:
            return XAPI_IMPORT_TYPE_LIVE;
    }
}
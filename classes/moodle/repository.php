<?php namespace logstore_emitter\xapi;
use \core\event\base as base_event;
use \stdClass as php_obj;

class repository extends php_obj {
    const VERSION = '1.0.1';
    protected $store;

    /**
     * Constructs a new repository.
     * @param php_obj $store
     * @param php_obj $cfg
     */
    public function __construct(php_obj $store, php_obj $cfg) {
        $this->store = $store;
        $this->cfg = $cfg;
    }

    /**
     * Reads a user from the store with the given id.
     * @param [string => mixed] $opts
     * @return php_obj
     */
    public function read_object(array $opts) {
        $restored_url = $this->restore_event($opts)->get_url();
        return (object) [
            'id' => $restored_url->getParam('id'),
            'url' => $this->generate_url($restored_url)
        ];
    }

    /**
     * Generates the object URL from the given URL.
     * @param php_obj $url
     * @return string URL
     */
    private function generate_url(php_obj $url) {
        return $url->get_scheme() . "://" . $url->get_host() . $url->get_path() . '?id=' . $url->get_param('id');
    }

    /**
     * Reads a user from the store with the given id.
     * @param string $user_id
     * @return php_obj
     */
    public function read_user($user_id) {
        $user = $this->store->get_record('user', ['id' => $user_id]);
        $user->url = $this->cfg->wwwroot . '/user/profile.php?id=' . $id;
        return $user;
    }

    /**
     * Restores an event.
     * @param [string => mixed] $opts
     * @return string
     */
    public function restore_event(array $opts) {
        $data = [
            'eventname' => $opts['eventname'],
            'component' => $opts['component'],
            'action' => $opts['action'],
            'target' => $opts['target'],
            'objecttable' => $opts['objecttable'],
            'objectid' => $opts['objectid'],
            'crud' => $opts['crud'],
            'edulevel' => $opts['edulevel'],
            'contextid' => $opts['contextid'],
            'contextlevel' => $opts['contextlevel'],
            'contextinstanceid' => $opts['contextinstanceid'],
            'userid' => $opts['userid'],
            'courseid' => $opts['courseid'],
            'relateduserid' => $opts['relateduserid'],
            'anonymous' => $opts['anonymous'],
            'other' => $opts['other'],
            'timecreated' => $opts['timecreated']
        ];

        $logextra = [
            'origin' => $opts['origin'],
            'ip' => $opts['ip'],
            'realuserid' => $opts['realuserid']
        ];

        return base_event::restore($data, $logextra);
    }
}

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
 * Report for extended log searching.
 *
 * @package    report_extendedlog
 * @copyright  2016 Vadim Dvorovenko <Vadimon@mail.ru>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_extendedlog\filter;

defined('MOODLE_INTERNAL') || die();

/**
 * Class for filtering by event.
 *
 * @package    report_extendedlog
 * @copyright  2016 Vadim Dvorovenko <Vadimon@mail.ru>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class eventname extends base {

    /** @var string */
    protected $event;

    /**
     * Returns list of plugins events.
     *
     * @return array
     */
    private function get_plugin_events() {
        $pluginman = \core_plugin_manager::instance();
        $eventslist = array();
        $types = \core_component::get_plugin_types();
        foreach ($types as $type => $typedirectory) {
            $plugins = \core_component::get_plugin_list($type);
            foreach ($plugins as $plugin => $plugindirectory) {
                $eventsdirectory = "$plugindirectory/classes/event";
                $events = $this->get_events($eventsdirectory, $type . '_' . $plugin);
                foreach ($events as $event => $eventdirectory) {
                    $eventname = '\\' . $type . '_' . $plugin . '\\event\\' . $event;
                    if ($this->method_exists($eventname, 'get_static_info')) {
                        $ref = new \ReflectionClass($eventname);
                        if (!$ref->isAbstract()) {
                            $eventinfo = new \stdClass();
                            $eventinfo->name = $eventname;
                            $eventinfo->displayname = $this->get_event_name($eventinfo->name);
                            $eventinfo->typedisplayname = $pluginman->plugintype_name($type);
                            $eventinfo->pluginname = $type . '_' . $plugin;
                            $eventinfo->plugindisplayname = $pluginman->plugin_name($eventinfo->pluginname);
                            $eventslist[$eventinfo->name] = $eventinfo;
                        }
                    }
                }
            }
        }
        return $eventslist;
    }

    /**
     * Returns list of core events.
     *
     * @return array
     */
    private function get_core_events() {
        global $CFG;

        $pluginman = \core_plugin_manager::instance();
        $eventslist = array();
        $eventsdirectory = "$CFG->libdir/classes/event";
        $events = $this->get_events($eventsdirectory, 'core');
        foreach ($events as $event => $eventdirectory) {
            $eventname = "\\core\\event\\$event";
            if ($this->method_exists($eventname, 'get_static_info')) {
                $ref = new \ReflectionClass($eventname);
                if (!$ref->isAbstract()) {
                    $eventinfo = new \stdClass();
                    $eventinfo->name = $eventname;
                    $eventinfo->displayname = $this->get_event_name($eventinfo->name);
                    $eventslist[$eventinfo->name] = $eventinfo;
                }
            }
        }
        return $eventslist;
    }

    /**
     * Returns a list of event files in specified directory.
     *
     * @param string $directory Location of files.
     * @param string $plugin Plugin name.
     * @return array Full location of files from the specified directory.
     */
    private function get_events($directory, $plugin) {
        global $CFG;
        $finaleventfiles = array();
        if (is_dir($directory)) {
            if ($handle = @opendir($directory)) {
                $eventfiles = scandir($directory);
                foreach ($eventfiles as $file) {
                    if ($file != '.' && $file != '..') {
                        $eventname = substr($file, 0, -4);
                        $finaleventfiles[$eventname] = $eventname;
                    }
                }
            }
        }
        return $finaleventfiles;
    }

    /**
     * Checks if the class method exists. Supresses warnings to hide warnings on deprecated events.
     *
     * @param string $object Class name.
     * @param string $methodname The method name.
     * @return bool <b>TRUE</b> if the method given by <i>method_name</i>
     * has been defined for the given <i>object</i>, <b>FALSE</b> otherwise.
     */
    private function method_exists ($object, $methodname) {
        global $CFG;

        $debuglevel          = $CFG->debug;
        $debugdisplay        = $CFG->debugdisplay;
        $debugdeveloper      = $CFG->debugdeveloper;
        $CFG->debug          = 0;
        $CFG->debugdisplay   = false;
        $CFG->debugdeveloper = false;

        $result = method_exists ($object, $methodname);

        $CFG->debug          = $debuglevel;
        $CFG->debugdisplay   = $debugdisplay;
        $CFG->debugdeveloper = $debugdeveloper;

        return $result;
    }

    /**
     * Returns localized event name. Supresses warnings to hide warnings on deprecated events.
     *
     * @param string $eventclassname Name of the event class.
     * @return string Localized event name.
     */
    private function get_event_name($eventclassname) {
        global $CFG;

        $debuglevel          = $CFG->debug;
        $debugdisplay        = $CFG->debugdisplay;
        $debugdeveloper      = $CFG->debugdeveloper;
        $CFG->debug          = 0;
        $CFG->debugdisplay   = false;
        $CFG->debugdeveloper = false;

        $name = $eventclassname::get_name();

        $CFG->debug          = $debuglevel;
        $CFG->debugdisplay   = $debugdisplay;
        $CFG->debugdeveloper = $debugdeveloper;

        return $name;
    }

    /**
     * Return list of events for dispalying on form. Caches list in session.
     *
     * @return array
     */
    public function get_events_list() {
        $cache = \cache::make_from_params(\cache_store::MODE_SESSION, 'report_extendedlog', 'menu');
        if ($eventslist = $cache->get('eventnames')) {
            return $eventslist;
        }

        $pluginevents = $this->get_plugin_events();
        $plugineventslist = array();
        foreach ($pluginevents as $event) {
            $groupname = get_string('filter_event_grouptemplate', 'report_extendedlog', $event);
            $displayname = get_string('filter_event_template', 'report_extendedlog', $event);
            $plugineventslist[$groupname][$event->name] = $displayname;
        }
        foreach ($plugineventslist as $group => $events) {
            \core_collator::asort($plugineventslist[$group]);
        }
        \core_collator::ksort($plugineventslist);

        $coreevents = $this->get_core_events();
        $coreeventslist = array();
        $groupname = get_string('filter_event_core', 'report_extendedlog');
        foreach ($coreevents as $event) {
            $displayname = get_string('filter_event_template', 'report_extendedlog', $event);
            $coreeventslist[$groupname][$event->name] = $displayname;
        }
        \core_collator::asort($coreeventslist[$groupname]);

        $strall = get_string('filter_event_all', 'report_extendedlog');
        $allevents = array($strall => array(0 => $strall));
        $eventslist = array_merge($allevents, $coreeventslist, $plugineventslist);

        $cache->set('eventnames', $eventslist);
        return $eventslist;
    }

    /**
     * Adds controls specific to this condition in the filter form.
     *
     * @param \MoodleQuickForm $mform Filter form
     */
    public function definition_callback(&$mform) {
        $events = $this->get_events_list();
        $mform->addElement('selectgroups', 'eventname', get_string('filter_event', 'report_extendedlog'), $events);
        $mform->setAdvanced('eventname', $this->advanced);
    }

    /**
     * Returns sql where part and params.
     *
     * @param array $data Form data or page paramenters as array
     * @param \moodle_database $db Database instance for creating proper sql
     * @return array($where, $params)
     */
    public function get_sql($data, $db) {
        if (!empty($data['eventname'])) {
            $where = 'eventname = :eventname';
            $params = array('eventname' => $data['eventname']);
        } else {
            $where = '';
            $params = array();
        }
        return array($where, $params);
    }

}

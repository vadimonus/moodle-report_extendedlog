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
use moodleform;

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->dirroot/report/eventlist/classes/list_generator.php");

/**
 * Class for filtering by event's component.
 *
 * @package    report_extendedlog
 * @copyright  2016 Vadim Dvorovenko <Vadimon@mail.ru>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class component extends base {

    /**
     * This function returns an array of all components for the plugins of the system.
     *
     * @return array A list of events from all plug-ins.
     */
    private function get_components_list() {
        $pluginman = \core_plugin_manager::instance();
        $plugininfo = $pluginman->get_plugins();
        foreach ($plugininfo as $type => $plugins) {
            $typelocal = $pluginman->plugintype_name_plural($type);
            foreach ($plugins as $name => $plugin) {
                $eventsdirectory = $plugin->rootdir . '/classes/event';
                if (is_dir($eventsdirectory)) {
                    $plugininfo = new \stdClass();
                    $plugininfo->type = $type;
                    $plugininfo->typelocal = $typelocal;
                    $plugininfo->component = $plugin->type . '_' . $plugin->name;
                    $plugininfo->displayname = get_string('filter_component_template', 'report_extendedlog', $plugin);
                    $plugininfoarray[] = $plugininfo;
                }
            }
        }
        return $plugininfoarray;
    }

    /**
     * Return list of components.
     *
     * @return array list of components.
     */
    private function get_components_menu() {
        $plugins = $this->get_components_list();
        $components = array();
        foreach ($plugins as $plugin) {
            $components[$plugin->typelocal][$plugin->component] = $plugin->displayname;
        }
        foreach ($components as $key => $plugins) {
            \core_collator::asort($components[$key]);
        }
        \core_collator::ksort($components);
        $topcomponents = array(
            get_string('filter_component_all', 'report_extendedlog') =>
                array('0' => get_string('filter_component_all', 'report_extendedlog')),
            get_string('filter_component_core', 'report_extendedlog') =>
                array('core' => get_string('filter_component_core', 'report_extendedlog')));
        $components = array_merge($topcomponents, $components);
        return $components;
    }

    /**
     * Adds controls specific to this condition in the filter form.
     *
     * @param \MoodleQuickForm $mform Filter form
     */
    public function add_filter_form_fields(&$mform) {
        $components = $this->get_components_menu();
        $mform->addElement('selectgroups', 'component', get_string('filter_component', 'report_extendedlog'), $components);
        $mform->setAdvanced('component', $this->advanced);
    }

}

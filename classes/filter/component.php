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
     * Traverse plugins folders and searches plugins.
     *
     * @return array
     */
    private function get_plugins() {
        $pluginman = \core_plugin_manager::instance();
        $pluginslist = array();
        $types = \core_component::get_plugin_types();
        foreach ($types as $type => $typedirectory) {
            $plugins = \core_component::get_plugin_list($type);
            foreach ($plugins as $plugin => $plugindirectory) {
                $eventsdirectory = "$plugindirectory/classes/event";
                if (is_dir($eventsdirectory)) {
                    $plugininfo = new \stdClass();
                    $plugininfo->name = $type . '_' . $plugin;
                    $plugininfo->displayname = $pluginman->plugin_name($plugininfo->name);
                    $plugininfo->typename = $type;
                    $plugininfo->typedisplaynameplural = $pluginman->plugintype_name_plural($type);
                    $pluginslist[$plugininfo->name] = $plugininfo;
                }
            }
        }
        return $pluginslist;
    }

    /**
     * Return list of components for dispalying on form. Caches list in session.
     *
     * @return array
     */
    private function get_components_list() {
        $cache = \cache::make_from_params(\cache_store::MODE_SESSION, 'report_extendedlog', 'menu');
        if ($componentslist = $cache->get('components')) {
            return $componentslist;
        }

        $plugins = $this->get_plugins();
        $componentslist = array();
        foreach ($plugins as $plugin) {
            $groupname = get_string('filter_component_grouptemplate', 'report_extendedlog', $plugin);
            $displayname = get_string('filter_component_template', 'report_extendedlog', $plugin);
            $componentslist[$groupname][$plugin->name] = $displayname;
        }
        foreach ($componentslist as $key => $plugins) {
            \core_collator::asort($componentslist[$key]);
        }
        \core_collator::ksort($componentslist);
        $topcomponents = array(
            get_string('filter_component_all', 'report_extendedlog') =>
                array('0' => get_string('filter_component_all', 'report_extendedlog')),
            get_string('filter_component_core', 'report_extendedlog') =>
                array('core' => get_string('filter_component_core', 'report_extendedlog')));
        $componentslist = array_merge($topcomponents, $componentslist);

        $cache->set('components', $componentslist);
        return $componentslist;
    }

    /**
     * Adds controls specific to this condition in the filter form.
     *
     * @param \MoodleQuickForm $mform Filter form
     */
    public function add_filter_form_fields(&$mform) {
        $components = $this->get_components_list();
        $mform->addElement('selectgroups', 'component', get_string('filter_component', 'report_extendedlog'), $components);
        $mform->setAdvanced('component', $this->advanced);
    }

}

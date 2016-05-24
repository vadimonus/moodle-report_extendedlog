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
 * Class for filtering by event's component.
 *
 * @package    report_extendedlog
 * @copyright  2016 Vadim Dvorovenko <Vadimon@mail.ru>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class component extends base {

    /** @var string */
    protected $component;

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
        $strall = get_string('filter_component_all', 'report_extendedlog');
        $strcore = get_string('filter_component_core', 'report_extendedlog');
        $topcomponents = array(
            $strall => array(0 => $strall),
            $strcore => array('core' => $strcore)
        );
        $componentslist = array_merge($topcomponents, $componentslist);

        $cache->set('components', $componentslist);
        return $componentslist;
    }

    /**
     * Adds controls specific to this condition in the filter form.
     *
     * @param \MoodleQuickForm $mform Filter form
     */
    public function definition_callback(&$mform) {
        $components = $this->get_components_list();
        $mform->addElement('selectgroups', 'component', get_string('filter_component', 'report_extendedlog'), $components);
        $mform->setAdvanced('component', $this->advanced);
    }

    /**
     * Returns sql where part and params.
     *
     * @param array $data Form data or page paramenters as array
     * @param \moodle_database $db Database instance for creating proper sql
     * @return array($where, $params)
     */
    public function get_sql($data, $db) {
        if (!empty($data['component'])) {
            $where = 'component = :component';
            $params = array('component' => $data['component']);
        } else {
            $where = '';
            $params = array();
        }
        return array($where, $params);
    }

}

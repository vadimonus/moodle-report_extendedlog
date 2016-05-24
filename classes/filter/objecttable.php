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
 * Class for filtering by objecttable.
 *
 * @package    report_extendedlog
 * @copyright  2016 Vadim Dvorovenko <Vadimon@mail.ru>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class objecttable extends base {

    /**
     * Return list of tables.
     *
     * @return array list of tables.
     */
    private function get_tables_list() {
        global $DB;

        $cache = \cache::make_from_params(\cache_store::MODE_SESSION, 'report_extendedlog', 'menu');
        if ($tableslist = $cache->get('objecttables')) {
            return $tableslist;
        }

        $tableslist = $DB->get_tables();
        \core_collator::asort($tableslist);
        $toptables = array(0 => get_string('filter_objecttable_all', 'report_extendedlog'));
        $tableslist = array_merge($toptables, $tableslist);

        $cache->set('objecttables', $tableslist);
        return $tableslist;
    }

    /**
     * Adds controls specific to this condition in the filter form.
     *
     * @param \MoodleQuickForm $mform Filter form
     */
    public function definition_callback(&$mform) {
        $users = $this->get_tables_list();
        $mform->addElement('select', 'objecttable', get_string('filter_objecttable', 'report_extendedlog'), $users);
        $mform->setAdvanced('objecttable', $this->advanced);
    }

    /**
     * Returns sql where part and params.
     *
     * @param array $data Form data or page paramenters as array
     * @param \moodle_database $db Database instance for creating proper sql
     * @return array($where, $params)
     */
    public function get_sql($data, $db) {
        if (!empty($data['objecttable'])) {
            $where = 'objecttable = :objecttable';
            $params = array('objecttable' => $data['objecttable']);
        } else {
            $where = '';
            $params = array();
        }
        return array($where, $params);
    }

}

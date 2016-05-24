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
 * Class for filtering by crud field.
 *
 * @package    report_extendedlog
 * @copyright  2016 Vadim Dvorovenko <Vadimon@mail.ru>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class origin extends base {

    /**
     * Return crud values.
     *
     * @return array list of users.
     */
    private function get_origin_list() {
        $originlist = array(
            'web' => get_string('filter_origin_web', 'report_extendedlog'),
            'cli' => get_string('filter_origin_cli', 'report_extendedlog'),
        );
        return $originlist;
    }

    /**
     * Adds controls specific to this condition in the filter form.
     *
     * @param \MoodleQuickForm $mform Filter form
     */
    public function definition_callback(&$mform) {
        $origins = $this->get_origin_list();
        $checkboxes = array();
        foreach ($origins as $key => $label) {
            $checkboxes[] = $mform->createElement('checkbox', $key, '', $label);
        }
        $mform->addGroup($checkboxes, 'origin', get_string('filter_origin', 'report_extendedlog'), ' ', true);
        $mform->setAdvanced('origin', $this->advanced);
    }

    /**
     * Returns sql where part and params.
     *
     * @param array $data Form data or page paramenters as array
     * @param \moodle_database $db Database instance for creating proper sql
     * @return array($where, $params)
     */
    public function get_sql($data, $db) {
        // If 2 items are selected, it means no filter needed.
        if (!empty($data['origin']) && count($data['origin'] != 2)) {
            $crud = array();
            foreach ($data['origin'] as $key => $value) {
                $crud[] = $key;
            }
            list($where, $params) = $db->get_in_or_equal($crud, SQL_PARAMS_NAMED, 'origin');
            $where = 'origin ' . $where;
        } else {
            $where = '';
            $params = array();
        }
        return array($where, $params);
    }

}

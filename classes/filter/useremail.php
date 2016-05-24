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
 * Сlass for filtering by user's email.
 *
 * @package    report_extendedlog
 * @copyright  2016 Vadim Dvorovenko <Vadimon@mail.ru>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class useremail extends base {

    /**
     * Adds controls specific to this condition in the filter form.
     *
     * @param \MoodleQuickForm $mform Filter form
     */
    public function definition_callback(&$mform) {
        $mform->addElement('text', 'useremail', get_string('filter_useremail', 'report_extendedlog'));
        $mform->setType('useremail', PARAM_TEXT);
        $mform->setAdvanced('useremail', $this->advanced);
    }

    /**
     * Returns sql where part and params.
     *
     * @param array $data Form data or page paramenters as array
     * @param \moodle_database $db Database instance for creating proper sql
     * @return array($where, $params)
     */
    public function get_sql($data, $db) {
        global $DB;

        if (!empty($data['useremail'])) {
            $where = $DB->sql_like('email', ":email", false, false);
            $params = array('email' => '%' . $DB->sql_like_escape($data['useremail']) . '%');
            $where = $where . ' AND deleted = :deleted';
            $params['deleted'] = 0;
            $users = $DB->get_fieldset_select('user', 'id', $where, $params);
            if (!empty($users)) {
                list($where, $params) = $db->get_in_or_equal($users, SQL_PARAMS_NAMED, 'useremail');
                $where = 'userid ' . $where;
            } else {
                $where = '1=0';
                $params = array();
            }
        } else {
            $where = '';
            $params = array();
        }
        return array($where, $params);
    }

}

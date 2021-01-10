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
 * Class for filtering by relateduser.
 *
 * @package    report_extendedlog
 * @copyright  2016 Vadim Dvorovenko <Vadimon@mail.ru>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class relateduser extends base {

    /**
     * Adds controls specific to this condition in the filter form.
     *
     * @param \MoodleQuickForm $mform Filter form
     */
    public function definition_callback(&$mform) {
        $options = [
            'ajax' => 'report_extendedlog/autocomplete-user',
            'multiple' => true,
            'noselectionstring' => get_string('filter_user_all', 'report_extendedlog'),
            'valuehtmlcallback' => function($value) {
                $fields = 'id, ' . get_all_user_name_fields(true);
                $user = \core_user::get_user($value, $fields);
                return fullname($user);
            }
        ];
        $mform->addElement(
            'autocomplete',
            'relateduser',
            get_string('filter_relateduser', 'report_extendedlog'),
            [],
            $options
        );
        $mform->setAdvanced('relateduser', $this->advanced);
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
        $where = '';
        $params = array();
        if (empty($data['relateduser'])) {
            return array($where, $params);
        }
        $users = $data['relateduser'];
        if (!is_array($users)) {
            $users = [$users];
        }
        list($where, $params) = $DB->get_in_or_equal($users, SQL_PARAMS_NAMED, 'user');
        $where = 'relateduserid ' . $where;
        return array($where, $params);
    }

}

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
 * Сlass for filtering by objectid.
 *
 * @package    report_extendedlog
 * @copyright  2016 Vadim Dvorovenko <Vadimon@mail.ru>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class objectid extends base {

    /**
     * Adds controls specific to this condition in the filter form.
     *
     * @param \MoodleQuickForm $mform Filter form
     */
    public function definition_callback(&$mform) {
        $mform->addElement('text', 'objectid', get_string('filter_objectid', 'report_extendedlog'));
        $mform->setType('objectid', PARAM_TEXT);
        $mform->setAdvanced('objectid', $this->advanced);
    }

    /**
     * Validates form data.
     *
     * @param array $data array of ("fieldname"=>value) of submitted data
     * @param array $files array of uploaded files "element_name"=>tmp_file_path
     * @return array of "element_name"=>"error_description" if there are errors, or an empty array if everything is OK.
     */
    public function validation_callback($data, $files) {
        $errors = array();
        if ($data['objectid'] !== '' && !is_number($data['objectid'])) {
            $errors['objectid'] = get_string('filter_objectid_error', 'report_extendedlog');
        }
        return $errors;
    }

    /**
     * Returns sql where part and params.
     *
     * @param array $data Form data or page paramenters as array
     * @param \moodle_database $db Database instance for creating proper sql
     * @return array($where, $params)
     */
    public function get_sql($data, $db) {
        if ($data['objectid'] !== '' && is_number($data['objectid'])) {
            $where = 'objectid = :objectid';
            $params = array('objectid' => $data['objectid']);
        } else {
            $where = '';
            $params = array();
        }
        return array($where, $params);
    }

}

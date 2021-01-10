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
 * Class for filtering by course.
 *
 * @package    report_extendedlog
 * @copyright  2016 Vadim Dvorovenko <Vadimon@mail.ru>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course extends base {
    /**
     * Adds controls specific to this condition in the filter form.
     *
     * @param \MoodleQuickForm $mform Filter form
     */
    public function definition_callback(&$mform) {
        $options = [
            'ajax' => 'report_extendedlog/autocomplete-course',
            'multiple' => true,
            'noselectionstring' => get_string('filter_course_all', 'report_extendedlog'),
            'valuehtmlcallback' => function($value) {
                global $DB;
                $course = $DB->get_record('course', ['id' => $value], 'fullname, shortname');
                if (!$course) {
                    return false;
                }
                return get_string('filter_course_template', 'report_extendedlog', $course);
            }
        ];
        $mform->addElement(
            'autocomplete',
            'course',
            get_string('filter_course', 'report_extendedlog'),
            [],
            $options
        );
        $mform->setAdvanced('course', $this->advanced);
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
        if (empty($data['course'])) {
            return array($where, $params);
        }
        $courses = $data['course'];
        if (!is_array($courses)) {
            $courses = [$courses];
        }
        list($where, $params) = $DB->get_in_or_equal($courses, SQL_PARAMS_NAMED, 'user');
        $where = 'courseid ' . $where;
        return array($where, $params);
    }

}

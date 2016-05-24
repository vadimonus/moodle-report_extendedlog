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
 * Class for filtering by course shortname.
 *
 * @package    report_extendedlog
 * @copyright  2016 Vadim Dvorovenko <Vadimon@mail.ru>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class courseshortname extends base {

    /**
     * Return list of users.
     *
     * @return array list of users.
     */
    private function get_courseshortnames_list() {
        global $DB;

        $cache = \cache::make_from_params(\cache_store::MODE_SESSION, 'report_extendedlog', 'menu');
        if ($courseshortnames = $cache->get('courseshortnames')) {
            return $courseshortnames;
        }

        $courses = $DB->get_records('course', array(), 'shortname', 'id,shortname');
        $courseshortnames = array();
        foreach ($courses as $course) {
            // Using string keys to prevent problems on sorting.
            $courseshortnames['a'.$course->id] = $course->shortname;
        }
        $sitename = $courseshortnames['a'.SITEID];
        unset($courseshortnames['a'.SITEID]);
        \core_collator::asort($courseshortnames);

        $topcourses = array(
            'a' => get_string('filter_courseshortname_all', 'report_extendedlog'),
            'a'.SITEID => $sitename);
        $courseshortnames = array_merge($topcourses, $courseshortnames);

        $cache->set('courseshortnames', $courseshortnames);
        return $courseshortnames;
    }

    /**
     * Adds controls specific to this condition in the filter form.
     *
     * @param \MoodleQuickForm $mform Filter form
     */
    public function definition_callback(&$mform) {
        $courseshortnames = $this->get_courseshortnames_list();
        $mform->addElement('select', 'courseshortname', get_string('filter_courseshortname', 'report_extendedlog'),
                $courseshortnames);
        $mform->setAdvanced('courseshortname', $this->advanced);
    }

    /**
     * Returns sql where part and params.
     *
     * @param array $data Form data or page paramenters as array
     * @param \moodle_database $db Database instance for creating proper sql
     * @return array($where, $params)
     */
    public function get_sql($data, $db) {
        $where = '';
        $params = array();
        if (empty($data['courseshortname'])) {
            return array($where, $params);
        }
        $course = substr($data['courseshortname'], 1);
        if (empty($course)) {
            return array($where, $params);
        }
        $where = 'courseid = :courseshortname';
        $params = array('courseshortname' => $course);
        return array($where, $params);
    }

}

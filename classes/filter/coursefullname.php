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
 * Class for filtering by course fullname.
 *
 * @package    report_extendedlog
 * @copyright  2016 Vadim Dvorovenko <Vadimon@mail.ru>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class coursefullname extends base {

    /**
     * Return list of users.
     *
     * @return array list of users.
     */
    private function get_coursefullnames_list() {
        global $DB;

        $cache = \cache::make_from_params(\cache_store::MODE_SESSION, 'report_extendedlog', 'menu');
        if ($coursefullnames = $cache->get('coursefullnames')) {
            return $coursefullnames;
        }

        $courses = $DB->get_records('course', array(), 'fullname', 'id,fullname');
        $coursefullnames = array();
        foreach ($courses as $course) {
            // Using string keys to prevent problems on sorting.
            $coursefullnames['a'.$course->id] = $course->fullname;
        }
        $sitename = $coursefullnames['a'.SITEID];
        unset($coursefullnames['a'.SITEID]);
        \core_collator::asort($coursefullnames);

        $topcourses = array(
            'a' => get_string('filter_coursefullname_all', 'report_extendedlog'),
            'a'.SITEID => $sitename);
        $coursefullnames = array_merge($topcourses, $coursefullnames);

        $cache->set('coursefullnames', $coursefullnames);
        return $coursefullnames;
    }

    /**
     * Adds controls specific to this condition in the filter form.
     *
     * @param \MoodleQuickForm $mform Filter form
     */
    public function definition_callback(&$mform) {
        $coursefullnames = $this->get_coursefullnames_list();
        $mform->addElement('select', 'coursefullname', get_string('filter_coursefullname', 'report_extendedlog'), $coursefullnames);
        $mform->setAdvanced('coursefullname', $this->advanced);
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
        if (empty($data['coursefullname'])) {
            return array($where, $params);
        }
        $course = substr($data['coursefullname'], 1);
        if (empty($course)) {
            return array($where, $params);
        }
        $where = 'courseid = :coursefullname';
        $params = array('coursefullname' => $course);
        return array($where, $params);
    }

}

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
 * The report_extendedlog report viewed event.
 *
 * @package    report_extendedlog
 * @copyright  2013 Ankit Agarwal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_extendedlog\event;

/**
 * The report_extendedlog report viewed event class.
 *
 * @property-read array $other {
 *      Extra information about the event.
 *
 *      - int groupid: Group to display.
 *      - int date: Date to display logs from.
 *      - int modid: Module id for which logs were displayed.
 *      - string modaction: Module action.
 *      - string logformat: Log format in which logs were displayed.
 * }
 *
 * @package    report_extendedlog
 * @since      Moodle 2.7
 * @copyright  2013 Ankit Agarwal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report_viewed extends \core\event\base {

    /**
     * Init method.
     *
     * @return void
     */
    protected function init() {
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }

    /**
     * Return localised event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventreportviewed', 'report_extendedlog');
    }

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return "The user with id '$this->userid' viewed the extended log search report.";
    }

    /**
     * Returns relevant URL.
     *
     * @return \moodle_url
     */
    public function get_url() {
        $params = $this->data['other'];
        $params['sesskey'] = sesskey();
        $params['_qf__report_extendedlog_filter_form'] = 1;
        $params['submitbutton'] = get_string('showlogs', 'report_extendedlog');
        $params = \report_extendedlog\filter_manager::fix_array_params($params);
        return new \moodle_url('/report/extendedlog/index.php', $params);
    }

}

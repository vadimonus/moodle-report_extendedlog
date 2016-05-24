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
 * Сlass for filtering by IP address.
 *
 * @package    report_extendedlog
 * @copyright  2016 Vadim Dvorovenko <Vadimon@mail.ru>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class ip6 extends base {

    /**
     * Adds controls specific to this condition in the filter form.
     *
     * @param \MoodleQuickForm $mform Filter form
     */
    public function definition_callback(&$mform) {
        $mform->addElement('text', 'ip6', get_string('filter_ip6', 'report_extendedlog'));
        $mform->setType('ip6', PARAM_TEXT);
        $mform->addHelpButton('ip6', 'filter_ip6', 'report_extendedlog');
        $mform->setAdvanced('ip6', $this->advanced);
    }

    /**
     * Returns sql where part and params.
     * Subnet strings can be only in full format xxxx:xxxx:xxxx:xxxx:xxxx:xxxx:xxxx (full IPv6 address)
     *
     * @param array $data Form data or page paramenters as array
     * @param \moodle_database $db Database instance for creating proper sql
     * @return array($where, $params)
     */
    public function get_sql($data, $db) {
        $where = '';
        $params = array();
        if (empty($data['ip6'])) {
            return array($where, $params);
        }

        $subnets = explode(',', $data['ip6']);
        foreach ($subnets as $key => $subnet) {
            $subnets[$key] = trim($subnet);
            if ($subnet === '') {
                unset($subnets[$key]);
            }
        }
        list($where, $params) = $db->get_in_or_equal($subnets, SQL_PARAMS_NAMED, 'ip6in');
        $where = 'ip ' . $where;
        return array($where, $params);
    }

}

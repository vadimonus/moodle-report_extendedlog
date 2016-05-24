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
class ip4 extends base {

    /** @var array For addresses in this array will be used LIKE search. */
    protected $addresseslike = array();

    /** @var array For addresses in this array will be used IN search. */
    protected $addressesin = array();

    /**
     * Adds controls specific to this condition in the filter form.
     *
     * @param \MoodleQuickForm $mform Filter form
     */
    public function definition_callback(&$mform) {
        $mform->addElement('text', 'ip4', get_string('filter_ip4', 'report_extendedlog'));
        $mform->setType('ip4', PARAM_TEXT);
        $mform->addHelpButton('ip4', 'filter_ip4', 'report_extendedlog');
        $mform->setAdvanced('ip4', $this->advanced);
    }

    /**
     * Returns sql where part and params.
     *
     * Subnet strings can be in one of formats:
     * 1: xxx.xxx.xxx.xxx-yyy (a range of IP addresses in the last group)
     * 2: xxx.xxx or xxx.xxx. (partial address)
     *
     * @param array $data Form data or page paramenters as array
     * @param \moodle_database $db Database instance for creating proper sql
     * @return array($where, $params)
     */
    public function get_sql($data, $db) {
        $where = '';
        $params = array();
        if (empty($data['ip4'])) {
            return array($where, $params);
        }

        $subnets = explode(',', $data['ip4']);
        foreach ($subnets as $subnet) {
            $subnet = trim($subnet);
            if ($subnet === '') {
                continue;
            }
            if (strpos($subnet, '-') !== false) {
                // 1: xxx.xxx.xxx.xxx-yyy A range of IP addresses in the last group.
                $this->get_ip_in_range($subnet);
            } else {
                // 2: xxx.xxx or xxx.xxx.
                $this->get_ip_partial($subnet);
            }
        }

        $conditions = array();
        $conditionsparams = array();
        if (!empty($this->addressesin)) {
            list($inwhere, $inparams) = $db->get_in_or_equal($this->addressesin, SQL_PARAMS_NAMED, 'ip4in');
            $inwhere = 'ip ' . $inwhere;
            $conditions[] = $inwhere;
            $conditionsparams = $inparams;
        }
        $i = 1;
        foreach ($this->addresseslike as $subnet) {
            $paramname = 'ip4like' . $i;
            $conditions[] = $db->sql_like('ip', ':'.$paramname);
            $conditionsparams[$paramname] = $subnet;
            $i++;
        }
        if (empty($conditions)) {
            return array($where, $params);
        }

        $where = '( ' . implode(' OR ', $conditions) . ' )';
        $params = $conditionsparams;

        return array($where, $params);
    }

    /**
     * Prepares search conditions for ip address range

     *
     * @param string $subnet
     * @return array
     */
    private function get_ip_in_range($subnet) {
        $parts = explode('-', $subnet);
        if (count($parts) != 2) {
            return;
        }
        $ipstart = cleanremoteaddr(trim($parts[0]), false); // Normalise.
        if ($ipstart === null) {
            return;
        }
        $ipparts = explode('.', $ipstart);
        $ipparts[3] = trim($parts[1]);
        $ipend = cleanremoteaddr(implode('.', $ipparts), false); // Normalise.
        if ($ipend === null) {
            return;
        }
        $iplist = array();
        for ($ip = ip2long($ipstart); $ip <= ip2long($ipend); $ip++) {
            $this->addressesin[] = long2ip($ip);
        }
    }

    /**
     * Prepares search conditions for partial ip address
     *
     * @param string $subnet
     */
    private function get_ip_partial($subnet) {
        $parts = explode('.', $subnet);
        $count = count($parts);
        if ($parts[$count - 1] === '') {
            unset($parts[$count - 1]); // Trim trailing dot.
            $count--;
            $subnet = implode('.', $parts);
        }
        if ($count == 4) {
            $subnet = cleanremoteaddr($subnet, false); // Normalise.
            $this->addressesin[] = $subnet;
        } else if ($count < 4) {
            $subnet = $subnet.'.%';
            $this->addresseslike[] = $subnet;
        }
    }

}

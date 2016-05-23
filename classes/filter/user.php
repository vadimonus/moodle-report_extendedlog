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
 * Class for filtering by user.
 *
 * @package    report_extendedlog
 * @copyright  2016 Vadim Dvorovenko <Vadimon@mail.ru>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user extends base {

    /**
     * Return list of users.
     *
     * @return array list of users.
     */
    private function get_users_list() {
        global $DB, $CFG;

        $cache = \cache::make_from_params(\cache_store::MODE_SESSION, 'report_extendedlog', 'menu');
        if ($usernames = $cache->get('users')) {
            return $usernames;
        }

        $fields = get_all_user_name_fields(true);
        $fields = "id,$fields";
        $users = $DB->get_records('user', array('deleted' => '0'), '', $fields);
        $usernames = array();
        foreach ($users as $user) {
            $usernames[$user->id] = fullname($user);
        }
        unset($usernames[$CFG->siteguest]);
        \core_collator::asort($usernames);
        $topusers = array(
            0 => get_string('filter_user_all', 'report_extendedlog'),
            $CFG->siteguest => get_string('guestuser'));
        $usernames = array_merge($topusers, $usernames);

        $cache->set('users', $usernames);
        return $usernames;
    }

    /**
     * Adds controls specific to this condition in the filter form.
     *
     * @param \MoodleQuickForm $mform Filter form
     */
    public function definition_callback(&$mform) {
        $users = $this->get_users_list();
        $mform->addElement('select', 'user', get_string('filter_user', 'report_extendedlog'), $users);
        $mform->setAdvanced('user', $this->advanced);
    }

}

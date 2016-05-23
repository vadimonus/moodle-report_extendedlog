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

require_once($CFG->libdir.'/coursecatlib.php');

/**
 * Class for filtering by category.
 *
 * @package    report_extendedlog
 * @copyright  2016 Vadim Dvorovenko <Vadimon@mail.ru>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class category extends base {

    /**
     * Return list of users.
     *
     * @return array list of users.
     */
    private function get_categories_list() {
        global $DB;

        $cache = \cache::make_from_params(\cache_store::MODE_SESSION, 'report_extendedlog', 'menu');
        if ($categories = $cache->get('categories')) {
            return $categories;
        }

        $categories = \coursecat::make_categories_list();
        $all = array(0 => get_string('filter_category_all', 'report_extendedlog'));
        $categories = array_merge($all, $categories);

        $cache->set('categories', $categories);
        return $categories;
    }

    /**
     * Adds controls specific to this condition in the filter form.
     *
     * @param \MoodleQuickForm $mform Filter form
     */
    public function add_filter_form_fields(&$mform) {
        $categories = $this->get_categories_list();
        $mform->addElement('select', 'category', get_string('filter_category', 'report_extendedlog'), $categories);
        $mform->setAdvanced('category', $this->advanced);
        $mform->addElement('checkbox', 'category_sub', get_string('filter_category_sub', 'report_extendedlog'));
        $mform->setAdvanced('category_sub', $this->advanced);
    }

}

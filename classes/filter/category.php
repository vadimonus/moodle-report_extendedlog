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

        $categorieslist = \coursecat::make_categories_list();
        $categories = array();
        foreach ($categorieslist as $key => $name) {
            $categories['a'.$key] = $name;
        }
        $all = array('a' => get_string('filter_category_all', 'report_extendedlog'));
        $categories = array_merge($all, $categories);

        $cache->set('categories', $categories);
        return $categories;
    }

    /**
     * Return category search options.
     *
     * @return array list of users.
     */
    private function get_searchoptions() {
        $searchoptions = array(
            'category' => get_string('filter_category_options_category', 'report_extendedlog'),
            'categories' => get_string('filter_category_options_subcategories', 'report_extendedlog'),
            'courses' => get_string('filter_category_options_courses', 'report_extendedlog'),
        );
        return $searchoptions;
    }

    /**
     * Adds controls specific to this condition in the filter form.
     *
     * @param \MoodleQuickForm $mform Filter form
     */
    public function definition_callback(&$mform) {
        $categories = $this->get_categories_list();
        $mform->addElement('select', 'category', get_string('filter_category', 'report_extendedlog'), $categories);
        $mform->setAdvanced('category', $this->advanced);
        $options = $this->get_searchoptions();
        $mform->addElement('select', 'categoryoptions', get_string('filter_category_options', 'report_extendedlog'), $options);
        $mform->setAdvanced('categoryoptions', $this->advanced);
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
        if (empty($data['category'])) {
            return array($where, $params);
        }
        $category = substr($data['category'], 1);
        if (empty($category)) {
            return array($where, $params);
        }

        $context = \context_coursecat::instance($category);
        $contexts = array($context->id);
        if (!empty($data['categoryoptions'])) {
            switch ($data['categoryoptions']) {
                case 'categories':
                    $contexts = array($context->id);
                    $path = $context->path;
                    $contextwhere = 'path LIKE :path AND contextlevel = :contextlevel';
                    $contextparams = array('path' => $context->path.'/%', 'contextlevel' => CONTEXT_COURSECAT);
                    $subcontexts = $DB->get_records_select('context', $contextwhere, $contextparams);
                    foreach ($subcontexts as $subcontext) {
                        $contexts[] = $subcontext->id;
                    }
                    break;
                case 'courses':
                    $contexts = array($context->id);
                    $path = $context->path;
                    $contextwhere = 'path LIKE :path';
                    $contextparams = array('path' => $context->path.'/%');
                    $subcontexts = $DB->get_records_select('context', $contextwhere, $contextparams);
                    foreach ($subcontexts as $subcontext) {
                        $contexts[] = $subcontext->id;
                    }
                    break;
            }
        }

        list($where, $params) = $db->get_in_or_equal($contexts, SQL_PARAMS_NAMED, 'contextid');
        $where = 'contextid ' . $where;
        return array($where, $params);
    }

}

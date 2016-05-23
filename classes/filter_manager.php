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

namespace report_extendedlog;

defined('MOODLE_INTERNAL') || die();

/**
 * Extended log search filter manager.
 *
 * @package    report_extendedlog
 * @copyright  2016 Vadim Dvorovenko <Vadimon@mail.ru>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class filter_manager {

    /** @var array The array of filters objects */
    protected $filters;

    /**
     * Constructor
     */
    public function __construct() {
        $this->filters = array();
        $filternames = array(
            'component' => 0,
            'event' => 0,
            'objecttable' => 1,
            'objectid' => 1,
            'crud' => 1,
            'edulevel' => 1,
            'category' => 0,
            'coursefullname' => 0,
            'courseshortname' => 1,
            'category' => 0,
            'user' => 0,
            'useremail' => 1,
            'relateduser' => 0,
            'timecreatedafter' => 0,
            'timecreatedbefore' => 0,
            'origin' => 1,
            'ip4' => 0,
            'ip6' => 1,
        );
        foreach ($filternames as $filtername => $advanced) {
            $fullfiltername = "\\report_extendedlog\\filter\\$filtername";
            $this->filters[] = new $fullfiltername($advanced);
        }
    }

    /**
     * Add filter items on filter form.
     *
     * @param \MoodleQuickForm $mform Filter form
     */
    public function add_filter_form_fields(&$mform) {
        foreach ($this->filters as $filter) {
            $filter->add_filter_form_fields($mform);
        }
    }

    /**
     * Parse data returned from form.
     *
     * @param object $data Data returned from $form->get_data()
     */
    public function process_form_data($data) {
        foreach ($this->filters as $filter) {
            $filter->process_form_data($data);
        }
    }

    /**
     * Validates form data.
     *
     * @param array $data array of ("fieldname"=>value) of submitted data
     * @param array $files array of uploaded files "element_name"=>tmp_file_path
     * @return array of "element_name"=>"error_description" if there are errors, or an empty array if everything is OK.
     */
    public function validate_form_data($data, $files) {
        $errors = array();
        foreach ($this->filters as $filter) {
            $filtererrors = $filter->validate_form_data($data, $files);
            $errors = array_merge($errors, $filtererrors);
        }
        return $errors;
    }

    /**
     * Returns array of request parameters, specific for filters.
     *
     * @return array
     */
    public function get_page_params() {
        $params = array();
        foreach ($this->filters as $filter) {
            $filterparams = $filter->get_page_params();
            $params = array_merge($params, $filterparams);
        }

        $logreader = optional_param('logreader', '', PARAM_COMPONENT);
        if (!empty($logreader)) {
            $params['logreader'] = $logreader;
        }
        return $params;
    }

    /**
     * Returns sql where part and params.
     *
     * @return array($where, $params)
     */
    public function get_sql() {
        $wherearray = array();
        $params = array();
        foreach ($this->filters as $filter) {
            list($filterwhere, $filterparams) = $filter->get_sql();
            if (!empty($filterwhere)) {
                $wherearray[] = $filterwhere;
                $params = array_merge($params, $filterparams);
            }
        }
        $where = implode(' AND ', $wherearray);
        /*if (empty($where)) {
            $where = '1=1';
        }*/
        return array($where, $params);
    }

}

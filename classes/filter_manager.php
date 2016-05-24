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
            'timecreatedafter' => 0,
            'timecreatedbefore' => 0,
            'user' => 0,
            'useremail' => 1,
            'relateduser' => 0,
            'category' => 0,
            'coursefullname' => 0,
            'courseshortname' => 1,
            'component' => 0,
            'eventname' => 0,
            'objecttable' => 1,
            'objectid' => 1,
            'crud' => 1,
            'edulevel' => 1,
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
    public function definition_callback(&$mform) {
        foreach ($this->filters as $filter) {
            $filter->definition_callback($mform);
        }
    }

    /**
     * Validates form data.
     *
     * @param array $data array of ("fieldname"=>value) of submitted data
     * @param array $files array of uploaded files "element_name"=>tmp_file_path
     * @return array of "element_name"=>"error_description" if there are errors, or an empty array if everything is OK.
     */
    public function validation_callback($data, $files) {
        $errors = array();
        foreach ($this->filters as $filter) {
            $filtererrors = $filter->validation_callback($data, $files);
            $errors = array_merge($errors, $filtererrors);
        }
        return $errors;
    }

    /**
     * Returns sql where part and params.
     *
     * @param array $data Form data or page paramenters as array
     * @param \core\log\reader $logreader Selected logreader
     * @return array($where, $params)
     */
    public function get_sql($data, $logreader) {
        global $DB;

        $wherearray = array();
        $where = array();
        $params = array();
        if ($logreader instanceof \logstore_standard\log\store) {
            $db = $DB;
        } else if ($logreader instanceof \logstore_database\log\store) {
            $db = $logreader->get_extdb();
        } else {
            return array($where, $params);
        }
        foreach ($this->filters as $filter) {
            list($filterwhere, $filterparams) = $filter->get_sql($data, $db);
            if (!empty($filterwhere)) {
                $wherearray[] = $filterwhere;
                $params = array_merge($params, $filterparams);
            }
        }
        $where = implode(' AND ', $wherearray);
        return array($where, $params);
    }

    /**
     * Convert array values from form to strings, to avoid moodle_url error.
     *
     * @param array $params Params from form
     * @return array
     */
    public static function fix_array_params($params) {
        $badparams = array();
        $newparams = array();
        foreach ($params as $name => $param) {
            if (is_array($param)) {
                $badparams[] = $name;
                foreach ($param as $key => $value) {
                    $fullname = $name . '[' . $key . ']';
                    $newparams[$fullname] = $value;
                }
            }
        }
        foreach ($badparams as $name) {
            unset($params[$name]);
        }
        $params = array_merge($params, $newparams);
        return $params;
    }

}

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
use moodleform;

defined('MOODLE_INTERNAL') || die();

/**
 * An abstract class for log filter filtering.
 *
 * @package    report_extendedlog
 * @copyright  2016 Vadim Dvorovenko <Vadimon@mail.ru>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class base {

    /** @var bool Should this filter be advanced on filter form. */
    protected $advanced;

    /**
     * Constructor
     *
     * @param bool $advanced Should this filter be advanced on filter form.
     */
    public function __construct($advanced) {
        $this->advanced = $advanced;
    }

    /**
     * Adds controls specific to this condition in the filter form.
     *
     * @param \MoodleQuickForm $mform Filter form
     */
    public abstract function add_filter_form_fields(&$mform);

    /**
     * Return an SQL fragment to be ANDed into the WHERE clause to filter which questions are shown.
     *
     * @return string SQL fragment. Must use named parameters.
     */
    //public abstract function where();

    /**
     * Return parameters to be bound to the above WHERE clause fragment.
     *
     * @return array parameter name => value.
     */
    /*public function params() {
        return array();
    }*/


    /**
     * Returns the condition to be used with SQL where
     * @param array $data filter settings
     * @return array sql string and $params
     */
    //public abstract function get_sql_filter($data);

        /**
    }
     * Display GUI for selecting criteria for this condition. Displayed always, whether Show More is open or not.
     *
     * Compare display_options_adv(), which displays when Show More is open.
     * @return string HTML form fragment
     */
    public function display_options() {
        return;
    }
}

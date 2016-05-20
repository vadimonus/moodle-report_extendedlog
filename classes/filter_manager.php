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
        $filternames = array('user' => 0, 'useremail' => 1, 'component' => 0, 'event' => 0);
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

}

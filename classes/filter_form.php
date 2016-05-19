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
use get_string;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');

/**
 * Extended log search form.
 *
 * @package    report_extendedlog
 * @copyright  2016 Vadim Dvorovenko <Vadimon@mail.ru>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class filter_form extends \moodleform {

    /**
     * Form definition method.
     */
    public function definition() {

        $mform = $this->_form;

        $mform->addElement('html', get_string('notificationhighload', 'report_extendedlog'));

        $logreaders = get_log_manager()->get_readers('core\log\sql_reader');
        foreach ($logreaders as $pluginname => $logreader) {
            $logreaders[$pluginname] = $logreader->get_name();
        }
        $mform->addElement('select', 'logreader', get_string('logstore', 'report_extendedlog'), $logreaders);

        $mform->addElement('header', 'filter', get_string('filterheader', 'report_extendedlog'));
        $filter_manager = $this->_customdata['filter_manager'];
        $filter_manager->add_filter_form_fields($mform);

        /*$mform->disable_form_change_checker();
        $componentarray = $this->_customdata['components'];
        $edulevelarray = $this->_customdata['edulevel'];
        $crudarray = $this->_customdata['crud'];

        $mform->addElement('header', 'displayinfo', get_string('filter', 'report_eventlist'));

        $mform->addElement('text', 'eventname', get_string('name', 'report_eventlist'));
        $mform->setType('eventname', PARAM_RAW);

        $mform->addElement('select', 'eventcomponent', get_string('component', 'report_eventlist'), $componentarray);
        $mform->addElement('select', 'eventedulevel', get_string('edulevel', 'report_eventlist'), $edulevelarray);
        $mform->addElement('select', 'eventcrud', get_string('crud', 'report_eventlist'), $crudarray);*/

        $this->add_action_buttons(true);
    }
}

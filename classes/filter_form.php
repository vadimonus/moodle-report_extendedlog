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

        $html = \html_writer::div(get_string('notificationhighload', 'report_extendedlog'), '',
                        array('style' => 'text-align: center; margin: 10px;'));
        $mform->addElement('html', $html);

        $enabledlogreaders = get_log_manager()->get_readers();
        $supportedlogreaders = get_log_manager()->get_supported_logstores('report_extendedlog');
        $logreaders = array();
        foreach ($enabledlogreaders as $pluginname => $logreader) {
            if (!empty($supportedlogreaders[$pluginname])) {
                $logreaders[$pluginname] = $logreader->get_name();
            }
        }
        $mform->addElement('select', 'logreader', get_string('logstore', 'report_extendedlog'), $logreaders);

        $mform->addElement('header', 'filter', get_string('filterheader', 'report_extendedlog'));

        $filtermanager = $this->_customdata['filter_manager'];
        $filtermanager->definition_callback($mform);

        $this->add_action_buttons(false, get_string('showlogs', 'report_extendedlog'));
    }

    /**
     * Form validation method.
     *
     * @param array $data array of ("fieldname"=>value) of submitted data
     * @param array $files array of uploaded files "element_name"=>tmp_file_path
     * @return array of "element_name"=>"error_description" if there are errors, or an empty array if everything is OK.
     */
    public function validation($data, $files) {
        $parenterrors = parent::validation($data, $files);

        $filtermanager = $this->_customdata['filter_manager'];
        $filterserrors = $filtermanager->validation_callback($data, $files);

        return array_merge($parenterrors, $filterserrors);
    }

    /**
     * Returns all form paramenters to use with paginator
     *
     * @return array
     */
    public function get_page_params() {
        $mform =& $this->_form;
        if (!$this->is_cancelled() and $this->is_submitted() and $this->is_validated()) {
            return $mform->exportValues();
        } else {
            return array();
        }
    }

}

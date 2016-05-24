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

require('../../config.php');
require_once($CFG->libdir.'/adminlib.php');

require_login();
$context = context_system::instance();
require_capability('report/extendedlog:view', $context);

$perpage = optional_param('perpage', '100', PARAM_INT); // How many per page.
$logformat = optional_param('download', '', PARAM_TEXT);

$url = new moodle_url('/report/extendedlog/index.php');
$PAGE->set_url($url);
$PAGE->set_context($context);
admin_externalpage_setup('reportextendedlog', '', null, '', array('pagelayout' => 'report'));
$PAGE->set_title(get_string('navigationnode', 'report_extendedlog'));
$PAGE->set_heading(get_string('navigationnode', 'report_extendedlog'));

$filtermanager = new \report_extendedlog\filter_manager();
$filterform = new \report_extendedlog\filter_form($url, array('filter_manager' => $filtermanager), 'get');

if ($filterform->is_submitted() && $pageparams = $filterform->get_page_params()) {
    if (!empty($pageparams['logreader'])) {

        // Create logreader.
        $manager = get_log_manager();
        $readers = $manager->get_readers();
        $logreader = $readers[$pageparams['logreader']];

        // Get sql parameters.
        list($where, $params) = $filtermanager->get_sql($pageparams, $logreader);

        // Table for printing log records.
        $logtable = new \report_extendedlog\logtable($logreader, $where, $params);
        $fixedparams = \report_extendedlog\filter_manager::fix_array_params($pageparams);
        $logtable->define_baseurl(new moodle_url($url, $fixedparams));
        $logtable->is_downloadable(true);
        $logtable->show_download_buttons_at(array(TABLE_P_BOTTOM));

        // Logging report viewing.
        $logparams = $pageparams;
        unset($logparams['submitbutton']);
        unset($logparams['sesskey']);
        unset($logparams['_qf__report_extendedlog_filter_form']);
        $eventdata = array('context' => $context, 'other' => $logparams);
        $event = \report_extendedlog\event\report_viewed::create($eventdata);
        $event->trigger();

        if (!empty($logformat)) {
            \core\session\manager::write_close();
            $logtable->download($logformat);
            die();
        } else {
            echo $OUTPUT->header();
            echo $OUTPUT->heading(get_string('navigationnode', 'report_extendedlog'));
            $filterform->display();
            $logtable->show($perpage);
            echo $OUTPUT->footer();
            die();
        }
    }
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('navigationnode', 'report_extendedlog'));
$filterform->display();
echo $OUTPUT->footer();

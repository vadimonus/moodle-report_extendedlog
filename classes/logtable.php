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

require_once("$CFG->libdir/tablelib.php");

/**
 * Extended log search filter manager.
 *
 * @package    report_extendedlog
 * @copyright  2016 Vadim Dvorovenko <Vadimon@mail.ru>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class logtable extends \report_log_table_log {

    /** @var \core\log\sql_reader */
    protected $logreader;

    /** @var string */
    protected $where;

    /** @var array */
    protected $params;

    /**
     * Constructor
     *
     * @param \core\log\sql_reader $logreader Log reader
     * @param string $where Where part of sql query
     * @param array $params Array of sql params
     */
    public function __construct($logreader, $where, $params) {

        $this->logreader = $logreader;
        $this->where = $where;
        $this->params = $params;

        $filterparams = new \stdClass();
        $filterparams->logreader = $this->logreader;
        $filterparams->courseid = SITEID;
        parent::__construct('report_extendedlog', $filterparams);

        $url = new \moodle_url('/report/extendedlog/index.php');
        $this->define_baseurl($url);
    }

    /**
     * Query the reader. Store results in the object for use by build_table.
     *
     * @param int $pagesize size of page for paginated displayed table.
     * @param bool $useinitialsbar do you want to use the initials bar.
     */
    public function query_db($pagesize, $useinitialsbar = true) {

        if (!$this->is_downloading()) {
            $total = $this->logreader->get_events_select_count($this->where, $this->params);
            $this->pagesize($pagesize, $total);
        } else {
            $this->pageable(false);
        }

        // Get the users data.
        $this->rawdata = $this->logreader->get_events_select_iterator($this->where, $this->params,
            'timecreated DESC', $this->get_page_start(), $this->get_page_size());
        $this->update_users_used();

        // We need to take it second time, as update_users_used closes iterator.
        $this->rawdata = $this->logreader->get_events_select_iterator($this->where, $this->params,
            'timecreated DESC', $this->get_page_start(), $this->get_page_size());

        // Set initial bars.
        if ($useinitialsbar && !$this->is_downloading()) {
            $this->initialbars($total > $pagesize);
        }

    }

    /**
     * Prints table.
     *
     * @param int $perpage Items to show perpage
     */
    public function show($perpage = 100) {
        $this->setup();
        $this->query_db($perpage, false);
        $this->build_table();
        $this->finish_output();
    }

    /**
     * Download table.
     *
     * @param string $logformat
     */
    public function download($logformat) {
        $filename = 'logs_' . userdate(time(), get_string('backupnameformat', 'langconfig'), 99, false);
        $this->is_downloading($logformat, $filename);
        $this->setup();
        $this->query_db(null, false);
        $this->build_table();
        $this->finish_output();
    }
}

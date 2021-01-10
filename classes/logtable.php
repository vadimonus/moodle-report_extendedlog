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

    /** @var array */
    protected $userfullnamesoverride = array();

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

        // Do not call $this->update_users_used() to prefetch user names, because it requires to query logs twice.
        // Multiple indexed requests to user table are much cheaper than second unindexed request to logs.
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
        // No need to raise execution time, as it's increased in \table_dataformat_export_format::__construct().
        // Giving extra memory limit. Not using HUGE or UNLIMITED, as this page may be used by multiple users at same time.
        raise_memory_limit(MEMORY_EXTRA);
        $filename = 'logs_' . userdate(time(), get_string('backupnameformat', 'langconfig'), 99, false);
        $this->is_downloading($logformat, $filename);
        $this->setup();
        $this->query_db(null, false);
        $this->build_table();
        $this->finish_output();
    }

    /**
     * Gets the user full name.
     *
     * Original function form report_log_table_log class has error - it calls get_records_sql() instead
     * of get_record_sql(). This is fixed version of this function. Using $this->userfullnamesoverride as
     * $this->userfullnames is declared private.
     *
     * @since Moodle 2.9
     * @param int $userid
     * @return string|false
     */
    protected function get_user_fullname($userid) {
        global $DB;

        if (empty($userid)) {
            return false;
        }

        if (!empty($this->userfullnamesoverride[$userid])) {
            return $this->userfullnamesoverride[$userid];
        }

        // We already looked for the user and it does not exist.
        if (isset($this->userfullnamesoverride[$userid]) && $this->userfullnamesoverride[$userid] === false) {
            return false;
        }

        // If we reach that point new users logs have been generated since the last users db query.
        list($usql, $uparams) = $DB->get_in_or_equal($userid);
        $sql = "SELECT id," . get_all_user_name_fields(true) . " FROM {user} WHERE id " . $usql;
        if (!$user = $DB->get_record_sql($sql, $uparams)) {
            return false;
        }

        $this->userfullnamesoverride[$userid] = fullname($user);
        return $this->userfullnamesoverride[$userid];
    }

    /**
     * Helper function to create list of user fullnames shown in log report.
     *
     * get_user_fullname function was overridden to use $this->userfullnamesoverride,
     * so we override this function to use this property too, in case we need this function
     * some day.
     *
     * @since   Moodle 2.9
     * @return  void
     */
    protected function update_users_used() {
        global $DB;

        $this->userfullnamesoverride = array();
        $userids = array();

        // For each event cache full username.
        // Get list of userids which will be shown in log report.
        foreach ($this->rawdata as $event) {
            $logextra = $event->get_logextra();
            if (!empty($event->userid) && empty($userids[$event->userid])) {
                $userids[$event->userid] = $event->userid;
            }
            if (!empty($logextra['realuserid']) && empty($userids[$logextra['realuserid']])) {
                $userids[$logextra['realuserid']] = $logextra['realuserid'];
            }
            if (!empty($event->relateduserid) && empty($userids[$event->relateduserid])) {
                $userids[$event->relateduserid] = $event->relateduserid;
            }
        }
        $this->rawdata->close();

        // Get user fullname and put that in return list.
        if (!empty($userids)) {
            list($usql, $uparams) = $DB->get_in_or_equal($userids);
            $users = $DB->get_records_sql("SELECT id," . get_all_user_name_fields(true) . " FROM {user} WHERE id " . $usql,
                $uparams);
            foreach ($users as $userid => $user) {
                $this->userfullnamesoverride[$userid] = fullname($user);
                unset($userids[$userid]);
            }

            // We fill the array with false values for the users that don't exist anymore
            // in the database so we don't need to query the db again later.
            foreach ($userids as $userid) {
                $this->userfullnamesoverride[$userid] = false;
            }
        }
    }
}

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
 * @copyright  2021 Vadim Dvorovenko <Vadimon@mail.ru>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_extendedlog\autocomplete;

use \external_value;
use \external_single_structure;
use \external_multiple_structure;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/externallib.php');

/**
 * Course search autocomplete api.
 *
 * @package    report_extendedlog
 * @copyright  2021 Vadim Dvorovenko <Vadimon@mail.ru>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course extends \external_api {
    /**
     * Parameter types.
     *
     * @return \external_function_parameters Parameters
     */
    public static function autocomplete_parameters() {
        return new \external_function_parameters([
            'query' => new external_value(PARAM_RAW, 'Query string'),
        ]);
    }

    /**
     * Returns result type.
     *
     * @return \external_description Result type
     */
    public static function autocomplete_returns() {
        return new external_multiple_structure(
            new external_single_structure([
                'id' => new external_value(PARAM_INT, 'Course id'),
                'name' => new external_value(PARAM_RAW, 'Course fullname and shortname'),
            ])
        );
    }

    /**
     * Performs search.
     *
     * @param string $query Query text
     * @return array Defined return structure
     */
    public static function autocomplete($query) {
        global $DB, $SITE;

        // Validate parameter.
        self::validate_parameters(self::autocomplete_parameters(), ['query' => $query]);

        // Validate the context.
        require_login();
        $context = \context_system::instance();
        self::validate_context($context);
        require_capability('report/extendedlog:view', $context);

        $where = '( id <> :siteid )';
        $params = [
            'siteid' => $SITE->id,
        ];
        if ($query) {
            $fullnamecondition = $DB->sql_position('UPPER(:fullname)', 'UPPER(fullname)');
            $shortnamecondition = $DB->sql_position('UPPER(:shortname)', 'UPPER(shortname)');
            $where .= " AND ( $fullnamecondition > 0 OR $shortnamecondition > 0 )";
            $params = array_merge($params, [
                'fullname' => $query,
                'shortname' => $query,
            ]);
        }
        $courses = $DB->get_records_select(
            'course',
            $where,
            $params,
            'fullname ASC, shortname ASC',
            'id, fullname, shortname',
            0,
            100
        );
        $result = [];
        $result[] = (object)[
            'id' => $SITE->id,
            'name' => get_string('filter_course_template', 'report_extendedlog', $SITE),
        ];
        foreach ($courses as $course) {
            $result[] = (object)[
                'id' => $course->id,
                'name' => get_string('filter_course_template', 'report_extendedlog', $course),
            ];
        }
        return $result;
    }
}

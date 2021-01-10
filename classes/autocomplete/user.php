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
 * User search autocomplete api.
 *
 * @package    report_extendedlog
 * @copyright  2021 Vadim Dvorovenko <Vadimon@mail.ru>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user extends \external_api {
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
                'id' => new external_value(PARAM_INT, 'User id'),
                'fullname' => new external_value(PARAM_RAW, 'Full name as text'),
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
        global $CFG;

        // Validate parameter.
        self::validate_parameters(self::autocomplete_parameters(), ['query' => $query]);

        // Validate the context.
        require_login();
        $context = \context_system::instance();
        self::validate_context($context);
        require_capability('report/extendedlog:view', $context);

        $users = \core_user::search($query);

        $result = [];
        $result[] = (object)[
            'id' => $CFG->siteguest,
            'fullname' => get_string('guestuser'),
        ];
        foreach ($users as $user) {
            $result[] = (object)[
                'id' => $user->id,
                'fullname' => fullname($user),
            ];
        }
        return $result;
    }
}

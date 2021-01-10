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
 * Course selector module.
 *
 * @module report_extendedlog/autocomplete-course
 * @class autocomplete-course
 * @package report_extendedlog
 * @copyright 2021 Vadim Dvorovenko
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Ajax from 'core/ajax';

export default {
    /**
     * Process the results for auto complete elements.
     *
     * @param {String} selector The selector of the auto complete element.
     * @param {Array} results An array or results.
     * @return {Array} New array of results.
     */
    processResults: function(selector, results) {
        return results.map((couse) => ({
            value: couse.id,
            label: couse.name
        }));
    },

    /**
     * Source of data for Ajax element.
     *
     * @param {String} selector The selector of the auto complete element.
     * @param {String} query The query string.
     * @param {Function} success A callback function receiving an array of results.
     */
    transport: function(selector, query, success) {
        // Call AJAX request.
        let promises = Ajax.call([{
            methodname: 'report_extendedlog_autocomplete_course',
            args: {
                query: query,
            }
        }]);

        // When AJAX request returns, handle the results.
        promises[0].then(success).catch(Notification.exception);
    }
};

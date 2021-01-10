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

defined('MOODLE_INTERNAL') || die;

$functions = [
    'report_extendedlog_autocomplete_category' => [
        'classname' => \report_extendedlog\autocomplete\category::class,
        'methodname' => 'autocomplete',
        'description' => 'Autocomplete for category field',
        'type' => 'read',
        'ajax' => true,
        'capabilities'  => 'report/extendedlog:view',
        'loginrequired' => true,
    ],
    'report_extendedlog_autocomplete_course' => [
        'classname' => \report_extendedlog\autocomplete\course::class,
        'methodname' => 'autocomplete',
        'description' => 'Autocomplete for course field',
        'type' => 'read',
        'ajax' => true,
        'capabilities'  => 'report/extendedlog:view',
        'loginrequired' => true,
    ],
    'report_extendedlog_autocomplete_user' => [
        'classname' => \report_extendedlog\autocomplete\user::class,
        'methodname' => 'autocomplete',
        'description' => 'Autocomplete for user field',
        'type' => 'read',
        'ajax' => true,
        'capabilities'  => 'report/extendedlog:view',
        'loginrequired' => true,
    ],
];
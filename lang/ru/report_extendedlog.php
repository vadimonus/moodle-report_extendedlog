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

$string['eventreportviewed'] = 'Отчет «Расширенный поиск в журнале событий» просмотрен';
$string['extendedlog:view'] = 'Просматривать отчет «Расширенный поиск в журнале событий»';
$string['filterheader'] = 'Фильтр';
$string['filter_category'] = 'Категория курсов';
$string['filter_category_all'] = 'Все категории';
$string['filter_category_options'] = 'Искать в';
$string['filter_category_options_category'] = 'Указанной категории';
$string['filter_category_options_subcategories'] = 'Указанной категории и во всех дочерных категориях';
$string['filter_category_options_courses'] = 'Указанной категории и во всех дочерних категориях, курсах, модулях курса';
$string['filter_coursefullname'] = 'Полное название курса';
$string['filter_coursefullname_all'] = 'Все курсы';
$string['filter_courseshortname'] = 'Краткое название курса';
$string['filter_courseshortname_all'] = 'Все курсы';
$string['filter_component'] = 'Компонент';
$string['filter_component_all'] = 'Все компоненты';
$string['filter_component_core'] = 'Ядро системы (core)';
$string['filter_component_grouptemplate'] = '{$a->typedisplaynameplural} ({$a->typename})';
$string['filter_component_template'] = '{$a->displayname} ({$a->name})';
$string['filter_crud'] = 'Действие';
$string['filter_ip4'] = 'Адрес IPv4';
$string['filter_ip4_help'] = 'Укажите через запятую список полных или частичных IP-адресов.

Примеры:

* 192.168.10.1
* 192.168.
* 231.3.56.10-20
* 192.168.10.1,192.168.,231.3.56.10-20';
$string['filter_ip6'] = 'Адрес IPv6';
$string['filter_ip6_help'] = 'Укажите через запятую список полных IP-адресов.';
$string['filter_edulevel'] = 'Образовательный уровень';
$string['filter_event'] = 'Событие';
$string['filter_event_all'] = 'Все событий';
$string['filter_event_core'] = 'События ядра системы (core)';
$string['filter_event_grouptemplate'] = '{$a->typedisplayname} «{$a->plugindisplayname}» ({$a->pluginname})';
$string['filter_event_template'] = '{$a->displayname} ({$a->name})';
$string['filter_objectid'] = 'id объекта';
$string['filter_objectid_error'] = 'Пожалуйста, укажите целое значение';
$string['filter_objecttable'] = 'Таблица объекта';
$string['filter_objecttable_all'] = 'Все таблицы';
$string['filter_origin'] = 'Источник';
$string['filter_origin_web'] = 'Веб-интерфейс';
$string['filter_origin_cli'] = 'Интерфейс командной строки';
$string['filter_relateduser'] = 'Затронутый пользователь';
$string['filter_timecreatedafter'] = 'Событие произошло после';
$string['filter_timecreatedbefore'] = 'Событие произошло до';
$string['filter_user'] = 'Пользователь';
$string['filter_useremail'] = 'Подстрока адреса электронной почты пользователя';
$string['filter_user_all'] = 'Все пользователи';
$string['logstore'] = 'Хранилище событий';
$string['navigationnode'] = 'Расширенный поиск в журнале событий';
$string['notificationhighload'] = 'Внимание! Этот отчет использует не оптимизированные запросы к базе данных. Такие запросы могут выполняться очень долго и привести к большой нагрузке на сервер базы данных.<br/>Для ускорения запросов настоятельно рекомендуется указывать временной интервал для поиска событий.';
$string['pluginname'] = 'Расширенный поиск в журнале событий';
$string['showlogs'] = 'Отобразить события';

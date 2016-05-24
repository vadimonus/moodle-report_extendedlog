Moodle plugin for extended log searching
========================================

Requirements
------------
- Moodle 2.9 (build 2015051100) or later.

Installation
------------
Copy the extendedlog folder into your Moodle /report directory and visit your Admin Notification page to complete the installation.

Usage
-----
This report is intended to be used only by the site administrator for investigation in number of cases, 
when standard log report does not allow to find the desired events. With this report you can easily do
following:
- Find out, who have granted role to user in some category a month ago
- List changes that were done on site by users from some IP or subnet
- Get events that happened in all courses in some category in one list
- Find failed logons for users using some email domain
- And so on

This report supports logstore_standart and logstore_database.

This report generates non-optimized db queries and may produce wery high database load.


Author
------
- Vadim Dvorovenko (Vadimon@mail.ru)

Links
-----
- Updates: https://moodle.org/plugins/view.php?plugin=report_extendedlog
- Latest code: https://github.com/vadimonus/moodle-report_extendedlog

Changes
-------
Release 1.0 (build 2016052402):
- First public release.

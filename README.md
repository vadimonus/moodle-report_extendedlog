Moodle plugin for extended log searching
========================================

Requirements
------------
- Moodle 3.5 (build 2018051700) or later.

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
- Release 1.0 (build 2016052402):
    - First public release.
- Release 1.1 (build 2020061300):
    - Privacy API support.
    - Fixes for deprecated coursecatlib
- Release 1.1.1 (build 2021010700):
    - Improvements for long queries. Raise memory limit, do not query logs twice to fetch user names.
- Release 1.1.2 (build 2021010701):
    - Privacy provider fix.
- Release 2.0 (build 2021011000):
    - Autocomplete elements for user, relateduser, course, category, event and objectable.
    - Ability to select multiple values for user, relateduser, course, event and objectable.

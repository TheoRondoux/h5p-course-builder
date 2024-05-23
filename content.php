<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Plugin strings are defined here.
 *
 * @package     h5plib_course_builder
 * @category    string
 * @copyright   2024 - Théo Rondoux
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @var admin_root $ADMIN
 * @var moodle_page $PAGE
 * @var moodle_database $DB
 * @var stdClass $CFG
 * @var site $SITE
 * @var stdClass $USER
 * @var core_renderer $OUTPUT
 */

use core_analytics\site;

require_once('../../../config.php');
require_once('./lib.php');
require_once($CFG->dirroot . '/h5p/h5plib/course_builder/extra/libs/attribute_lib.php');

require_login();
h5plib_course_builder_no_access_redirect($USER);

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/h5p/h5plib/course_builder/content.php'));
$PAGE->set_pagelayout('standard');

$type = optional_param('type', null, PARAM_TEXT);
$back_url = new moodle_url('/h5p/h5plib/course_builder/');

$presentations = [];

if ($type == null) {
    $pageTitle = 'mypresentationstitle';
    $presentations =
            $DB->get_records_sql('SELECT mdl_hvp.id, mdl_hvp.name, mdl_hvp.timecreated, mdl_hvp.timemodified, mdl_h5plib_course_builder_pres.userid, mdl_h5plib_course_builder_pres.shared FROM mdl_hvp,mdl_h5plib_course_builder_pres WHERE mdl_hvp.id IN (SELECT presentationid FROM mdl_h5plib_course_builder_pres WHERE userid = ' .
                    $USER->id .
                    ') AND mdl_hvp.id = mdl_h5plib_course_builder_pres.presentationid ORDER BY mdl_hvp.timemodified DESC');
} else if ($type == 'shared') {
    $pageTitle = 'sharedpresentationstitle';
    $presentations =
            $DB->get_records_sql('SELECT mdl_hvp.id, mdl_hvp.name, mdl_hvp.course, mdl_hvp.timecreated, mdl_hvp.timemodified, mdl_user.firstname, mdl_user.lastname, mdl_h5plib_course_builder_pres.userid, mdl_h5plib_course_builder_pres.shared FROM mdl_hvp,mdl_h5plib_course_builder_pres, mdl_user WHERE mdl_h5plib_course_builder_pres.shared = 1 AND mdl_hvp.id = mdl_h5plib_course_builder_pres.presentationid AND mdl_h5plib_course_builder_pres.userid != ' .
                    $USER->id . ' AND mdl_h5plib_course_builder_pres.userid = mdl_user.id ');
} else {
    h5plib_course_builder_redirect_error('Invalid type given.');
}

$PAGE->set_heading(get_string($pageTitle, 'h5plib_course_builder'));
$PAGE->set_title(get_string($pageTitle, 'h5plib_course_builder') . " " . $SITE->fullname);

h5plib_course_builder_display_logo();
echo $OUTPUT->header();

echo html_writer::tag('a', get_string('back', 'h5plib_course_builder'), h5plib_course_builder_get_back_btn_attributes());

h5plib_course_builder_display_all_presentations($presentations, $USER);
echo $OUTPUT->footer();

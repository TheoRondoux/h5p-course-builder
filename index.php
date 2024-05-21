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
 * @package     h5plib_poc_editor
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
require_login();
h5plib_poc_editor_no_access_redirect($USER);

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/h5p/h5plib/poc_editor/index.php'));
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('pluginname', 'h5plib_poc_editor') . " " . $SITE->fullname);
$PAGE->set_heading(get_string('pluginname', 'h5plib_poc_editor'));

$userPresentations =
        $DB->get_records_sql('SELECT mdl_hvp.id, mdl_hvp.name, mdl_hvp.timecreated, mdl_hvp.timemodified, mdl_h5plib_poc_editor_pres.userid, mdl_h5plib_poc_editor_pres.shared FROM mdl_hvp,mdl_h5plib_poc_editor_pres WHERE mdl_hvp.id IN (SELECT presentationid FROM mdl_h5plib_poc_editor_pres WHERE userid = ' .
                $USER->id . ') AND mdl_hvp.id = mdl_h5plib_poc_editor_pres.presentationid ORDER BY mdl_hvp.timemodified DESC');
$sharedPresentations =
        $DB->get_records_sql('SELECT mdl_hvp.id, mdl_hvp.name, mdl_hvp.course, mdl_hvp.timecreated, mdl_hvp.timemodified, mdl_user.firstname, mdl_user.lastname, mdl_h5plib_poc_editor_pres.userid, mdl_h5plib_poc_editor_pres.shared FROM mdl_hvp,mdl_h5plib_poc_editor_pres, mdl_user WHERE mdl_h5plib_poc_editor_pres.shared = 1 AND mdl_hvp.id = mdl_h5plib_poc_editor_pres.presentationid AND mdl_h5plib_poc_editor_pres.userid != ' .
                $USER->id . ' AND mdl_h5plib_poc_editor_pres.userid = mdl_user.id ');

echo $OUTPUT->header();
if (is_siteadmin()) {
    $settings_url = new moodle_url('/h5p/h5plib/poc_editor/configuration.php');
    echo html_writer::tag('a', get_string('settings', 'h5plib_poc_editor'), ['href' => $settings_url ]);

}

h5plib_poc_editor_delete_user_enrolments($USER);
echo html_writer::tag('br', '');

// echo html_writer::start_tag('div');
echo html_writer::start_tag('div', ['class' => 'd-grid gap-2 col-6 mx-auto']);
echo html_writer::tag('a', get_string('createnewpresentation', 'h5plib_poc_editor'), ['href' => 'creation_form.php' , 'role' => 'button','class' => 'btn btn-block create-btn']);
echo html_writer::end_tag('div');
echo html_writer::tag('br', '');
echo html_writer::tag('h3', get_string('mypresentationstitle', 'h5plib_poc_editor'));
if ($userPresentations && count($userPresentations) < 7) {
    h5plib_poc_editor_display_all_presentations($userPresentations, $USER);
} else if ($userPresentations && count($userPresentations) > 6) {
    h5plib_poc_editor_display_some_presentations($userPresentations, $USER, 6);
    echo '<center><a href="presentations.php">' . get_string('showpresentations', 'h5plib_poc_editor') . '</a></center>';
} else {
    echo html_writer::start_tag('center');
    echo html_writer::tag('p', get_string('nopresentation', 'h5plib_poc_editor'));
    echo html_writer::end_tag('center');
}

echo html_writer::tag('br', '');
echo html_writer::tag('h3', get_string('sharedpresentationstitle', 'h5plib_poc_editor'));

if ($sharedPresentations && count($sharedPresentations) < 7) {
    h5plib_poc_editor_display_all_presentations($sharedPresentations, $USER);
} else if ($sharedPresentations && count($sharedPresentations) > 6) {
    h5plib_poc_editor_display_some_presentations($sharedPresentations, $USER, 6);
    echo '<center><a href="' . new moodle_url('presentations.php', ['type' => 'shared']) . '">' . get_string('showsharedpresentations', 'h5plib_poc_editor') . '</a></center>';
} else {
    echo html_writer::start_tag('center');
    echo html_writer::tag('p', get_string('nosharedpresentations', 'h5plib_poc_editor'));
    echo html_writer::end_tag('center');
}


echo $OUTPUT->footer();
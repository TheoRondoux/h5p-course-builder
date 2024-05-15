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
*/

require_once('../../../config.php');
require_once('./lib.php');
require_login();

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/h5p/h5plib/poc_editor/index.php'));
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('pluginname', 'h5plib_poc_editor') . " " . $SITE->fullname);
$PAGE->set_heading(get_string('pluginname', 'h5plib_poc_editor'));

$userpresentations = $DB->get_records_sql('SELECT mdl_hvp.id, mdl_hvp.name, mdl_hvp.timecreated, mdl_hvp.timemodified, mdl_h5plib_poc_editor_pres.shared FROM mdl_hvp,mdl_h5plib_poc_editor_pres WHERE mdl_hvp.id IN (SELECT presentationid FROM mdl_h5plib_poc_editor_pres WHERE userid = '.$USER->id.') AND mdl_hvp.id = mdl_h5plib_poc_editor_pres.presentationid ORDER BY mdl_hvp.timemodified DESC');
$sharedpresentations = $DB->get_records_sql('SELECT mdl_hvp.id, mdl_hvp.name, mdl_hvp.timecreated, mdl_hvp.timemodified, mdl_user.firstname, mdl_user.lastname FROM mdl_hvp,mdl_h5plib_poc_editor_pres, mdl_user WHERE mdl_h5plib_poc_editor_pres.shared = 1 AND mdl_hvp.id = mdl_h5plib_poc_editor_pres.presentationid AND mdl_h5plib_poc_editor_pres.userid != ' . $USER->id . ' AND mdl_h5plib_poc_editor_pres.userid = mdl_user.id ');

echo $OUTPUT->header();

if (is_siteadmin()) {
    $settings_url = new moodle_url('/h5p/h5plib/poc_editor/configuration.php');
    echo html_writer::tag('a', 'Settings', ['href' => $settings_url , 'role' => 'button','class' => 'btn btn-primary btn-sm', 'data-bs-toggle' => 'button', 'aria-pressed' => 'true', 'style' => ' background-color: #3F2A56; border-color: #3F2A56; padding:8px 15px 8px 15px; margin-top: 10px;']);

}
echo html_writer::tag('br', '');

echo html_writer::start_tag('div', ['class' => 'd-grid gap-2 col-6 mx-auto']);
echo html_writer::tag('a', 'Create New Presentation', ['href' => 'creation_form.php' , 'role' => 'button','class' => 'btn btn-primary btn-sm', 'style' => ' background-color: #3F2A56; border-color: #3F2A56; padding:12px 20px 12px 20px;']);

echo html_writer::end_tag('div');

echo html_writer::tag('br', '');

echo html_writer::tag('h4', 'My Presentations');


if ($userpresentations && count($userpresentations) < 6) {
    h5plib_poc_editor_display_all_presentations($userpresentations);
} 
else if ($userpresentations && count($userpresentations) > 5) {
    h5plib_poc_editor_display_some_presentations($userpresentations, 6);

    echo html_writer::start_tag('center');
    echo html_writer::tag('a', 'Show All My Presentations', ['href' => '#' , 'role' => 'button','class' => 'btn btn-primary btn-sm', 'data-bs-toggle' => 'button', 'aria-pressed' => 'true', 'style' => ' background-color: #3F2A56
    ; border-color: #3F2A56; padding:12px 20px 12px 20px; margin-top: 10px;']);
    echo html_writer::end_tag('center');

}
else {
    echo html_writer::start_tag('center');
    echo html_writer::tag('p', 'No presentations created yet');
    echo html_writer::end_tag('center');
}

<<<<<<< HEAD
echo html_writer::tag('br', '');

echo html_writer::tag('h3', 'Shared Presentations');
=======
echo html_writer::tag('h4', 'My presentations');
>>>>>>> c067e85b88d2ad5d25d29cc710265e073b7970c6

if (count($sharedpresentations) > 0) {
    echo $OUTPUT->box_start('card-columns');
    echo html_writer::start_tag('div', ['class' => 'shared-pres']);
    foreach($sharedpresentations as $sharedpres) {
        $moduleid = $DB->get_record('course_modules', ['instance' => $sharedpres->id])->id;
        $courseviewurl = '<a href="'.new moodle_url("/mod/hvp/view.php?id=".$moduleid."&forceview=1").'">' . $sharedpres->name . '</a>';
        echo html_writer::start_tag('div', ['class' => 'card']);
        echo html_writer::start_tag('div', ['class' => 'card-body']);
        echo html_writer::empty_tag('img', ['src' => 'https://picsum.photos/200/300', 'class' => 'card-img-top', 'alt' => 'Card image']);
        echo html_writer::tag('p', $courseviewurl , ['class' => 'card-text']);
        echo html_writer::tag('small', 'By ' . $sharedpres->firstname . ' ' . $sharedpres->lastname, ['class' => 'text-muted']);
        echo html_writer::start_tag('p', ['class' => 'card-text']);
        echo html_writer::tag('small', userdate($sharedpres->timecreated), ['class' => 'text-muted']);
        echo html_writer::end_tag('p');
        echo html_writer::end_tag('div');
        echo html_writer::end_tag('div');
    }
    echo html_writer::end_tag('div');
    echo $OUTPUT->box_end();
}
else {
    echo html_writer::start_tag('center');
    echo html_writer::tag('p', 'No presentations shared with you for the moment.');
    echo html_writer::end_tag('center');
}

<<<<<<< HEAD
=======
echo html_writer::start_tag('div', ['class' => 'new-pres']);
echo html_writer::tag('a', 'Create New Presentation', ['href' => 'creation_form.php' , 'role' => 'button','class' => 'btn btn-primary btn-sm', 'data-bs-toggle' => 'button', 'aria-pressed' => 'true', 'style' => ' background-color: #3F2A56; padding:12px 20px 12px 20px; margin-top: 10px;']);
echo html_writer::end_tag('div');

>>>>>>> c067e85b88d2ad5d25d29cc710265e073b7970c6

echo $OUTPUT->footer();
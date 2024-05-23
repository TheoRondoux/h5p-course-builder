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
 * @package     h5plib_course_builder
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

require_once('../../../config.php');
require_once('./libs/lib.php');
require_once($CFG->dirroot . '/h5p/h5plib/course_builder/libs/attribute_lib.php');

require_login();
h5plib_course_builder_no_access_redirect($USER);

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/h5p/h5plib/course_builder/details.php'));
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('pluginname', 'h5plib_course_builder') . " " . $SITE->fullname);

$presentationId = required_param('id', PARAM_INT);
$presentationDetails = $DB->get_record('hvp', ['id' => $presentationId], 'course, name, timecreated, timemodified');
if (empty($presentationDetails)) {
    h5plib_course_builder_redirect_error('A presentation with the id ' . $presentationId . ' does not exits');
}
$moduleId = $DB->get_record('course_modules', ['instance' => $presentationId])->id;
$relatedCourse = $DB->get_record('course', ['id' => $presentationDetails->course]);
$presentationEditorInfo = $DB->get_record('h5plib_course_builder_pres', ['presentationid' => $presentationId], 'shared, userid');

if (!$presentationEditorInfo->shared && $USER->id != $presentationEditorInfo->userid) {
    h5plib_course_builder_redirect_error('You do not have permission to see this presentation');
} else if ($presentationEditorInfo->shared && $USER->id != $presentationEditorInfo->userid) {
    $enrolInstance = $DB->get_record('enrol', ['courseid' => $relatedCourse->id, 'enrol' => 'manual']);
    $enrolplugin = enrol_get_plugin($enrolInstance->enrol);
    $enrolplugin->enrol_user($enrolInstance, $USER->id, $DB->get_record('role', ['shortname' => 'teacher'], 'id')->id, time(),
            strtotime('+1 minute', time()));
}

$author = $DB->get_record('user', ['id' => $presentationEditorInfo->userid], 'firstname, lastname');
h5plib_course_builder_display_logo();
$PAGE->set_heading($relatedCourse->fullname);

$editUrl = new moodle_url("/course/modedit.php", ['update' => $moduleId, 'return' => 1]) . "#fgroup_id_h5peditor";

echo $OUTPUT->header();

echo html_writer::start_tag('center');
echo html_writer::tag('h4', $presentationDetails->name);
echo html_writer::end_tag('center');
if ($presentationEditorInfo->userid != $USER->id) {
    echo html_writer::tag('p', get_string('by', 'h5plib_course_builder') . " " . $author->firstname . " " . $author->lastname);
}
echo html_writer::tag('a', get_string('back', 'h5plib_course_builder'),h5plib_course_builder_get_back_btn_attributes());
echo html_writer::tag('br', '');
if ($presentationEditorInfo->userid == $USER->id) {
    echo html_writer::start_tag('div', ['class' => 'details']);
    echo html_writer::tag('h5', get_string('details', 'h5plib_course_builder'));
    echo html_writer::tag('p', get_string('createdat', 'h5plib_course_builder') . userdate($presentationDetails->timecreated));
    echo html_writer::tag('p', get_string('lastmodified', 'h5plib_course_builder') . userdate($presentationDetails->timemodified));
    echo html_writer::end_tag('div');
}

echo html_writer::tag('br', '');
echo html_writer::start_tag('div', ['class' => 'preview']);
echo html_writer::tag('h5', get_string('preview', 'h5plib_course_builder'));

if ($presentationEditorInfo->userid == $USER->id) {
echo html_writer::tag('a', get_string('edit', 'h5plib_course_builder'), h5plib_course_builder_get_edit_btn_attributes($editUrl));
}

echo '<iframe src="' . new moodle_url("/mod/hvp/embed.php?id=" . $moduleId) .
        '" width="1377" height="800" frameborder="0" allowfullscreen="allowfullscreen" title="' . $presentationDetails->name .
        '"></iframe><script src="' .
        new moodle_url('/mod/hvp/library/js/h5p-resizer.js') . '" charset="UTF-8"></script>';
echo html_writer::end_tag('div');
echo $OUTPUT->footer();

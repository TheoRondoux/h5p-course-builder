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
require_once($CFG->dirroot . '/h5p/h5plib/poc_editor/libs/lib.php');
require_once($CFG->dirroot . '/course/modlib.php');
require_once($CFG->dirroot . '/h5p/h5plib/poc_editor/libs/attribute_functions.php');
require_login();
h5plib_poc_editor_no_access_redirect($USER);

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/h5p/h5plib/poc_editor/creation_form.php'));
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('pluginname', 'h5plib_poc_editor') . " " . $SITE->fullname);
display_course_builder_logo();
$PAGE->set_heading(get_string('creationtitle', 'h5plib_poc_editor'));

$createCourseForm = new \h5plib_poc_editor\form\course_creation_form();

$rowTemplates = h5p_poc_editor_get_added_templates();
if (count($rowTemplates) < 1) {
    h5plib_poc_editor_redirect_error(get_string('notemplateerror', 'h5plib_poc_editor'));
}

if ($data = $createCourseForm->get_data()) {
    $title = required_param('presentation_title', PARAM_TEXT);
    $selected_course_index = required_param('course_select', PARAM_INT);
    $selected_template_index = required_param('template_select', PARAM_INT);
    $introduction = optional_param('presentation_intro', "", PARAM_TEXT);
    $shared = optional_param('share_presentation', 0, PARAM_INT);

    if (!empty($title) && !empty($selected_course_index)) {
        $retrieved_course = h5p_poc_editor_find_course($selected_course_index, h5p_poc_editor_get_courses());
        $course = get_course($retrieved_course->id);

        $template = h5p_poc_editor_find_template($selected_template_index);
        $newModule = h5plib_poc_editor_generate_module($title, $template, $introduction, 'hvp');
        $result = add_moduleinfo($newModule, $course);

        if ($result->instance) {
            $savePresentation = new stdClass();
            $savePresentation->userid = $USER->id;
            $savePresentation->presentationid = $result->instance;
            $savePresentation->shared = $shared;

            $DB->insert_record('h5plib_poc_editor_pres', $savePresentation);
        }

        redirect(new moodle_url('/h5p/h5plib/poc_editor'), get_string('presentationcreated', 'h5plib_poc_editor'), null,
                \core\output\notification::NOTIFY_SUCCESS);
    }
}

echo $OUTPUT->header();
echo html_writer::tag('a', get_string('back', 'h5plib_poc_editor'), get_back_btn_attributes());

$createCourseForm->display();
echo $OUTPUT->footer();

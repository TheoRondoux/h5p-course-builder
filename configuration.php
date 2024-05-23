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
require_once($CFG->dirroot . '/course/classes/category.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once('./libs/lib.php');
require_once($CFG->dirroot . '/h5p/h5plib/poc_editor/libs/attribute_lib.php');

require_login();
if (!is_siteadmin()) {
    h5plib_poc_editor_redirect_error(get_string('noaccesstosettings', 'h5plib_poc_editor'));
}

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/h5p/h5plib/poc_editor/configuration.php'));
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('pluginname', 'h5plib_poc_editor') . " " . $SITE->fullname);
$PAGE->set_heading(get_string('configtitle', 'h5plib_poc_editor'));

display_course_builder_logo();
$configForm = new \h5plib_poc_editor\form\config_form();
$addTemplateForm = new \h5plib_poc_editor\form\config_add_template_form();
$updateTemplateForm = new \h5plib_poc_editor\form\config_update_template_form();
$deleteTemplateForm = new \h5plib_poc_editor\form\config_delete_template_form();

if ($data = $configForm->get_data()) {

    $categoryName = "Poc Editor";
    $dbCategory = $DB->get_record('course_categories', ["name" => $categoryName]);
    $categoryId = 0;
    if (!$dbCategory) {
        $newCategory = new stdClass();
        $newCategory->name = $categoryName;
        $newCategory->description = get_string('categorydescription', 'h5plib_poc_editor');
        $newCategory->visible = 0;
        $newCategory->idnumber = '';

        $createdCategory = core_course_category::create($newCategory);
        $categoryId = $createdCategory->id;
    } else {
        $categoryId = $dbCategory->id;
    }

    $newCourse = new stdClass();
    $newCourse->shortname = "poceditor";
    $newCourse->fullname = "Poc Editor";
    $newCourse->category = $categoryId;
    $newCourse->visible = 0;

    $createdCourse = create_course($newCourse);
    redirect(new moodle_url('/h5p/h5plib/poc_editor/configuration.php'), get_string('courseadded', 'h5plib_poc_editor'), null,
            \core\output\notification::NOTIFY_SUCCESS);
}

if ($data = $addTemplateForm->get_data()) {

    if (isset($data->available_templates)) {
        $templateIndex = $data->available_templates;
        $templateCourseId = h5p_poc_editor_get_template_course()->id;
        $addedTemplates = h5p_poc_editor_get_added_templates();
        $availableTemplates = h5p_poc_editor_get_available_templates($addedTemplates, $templateCourseId);

        $chosenTemplate = $availableTemplates[$templateIndex];

        $newTemplate = new stdClass();
        $newTemplate->presentationid = $chosenTemplate->id;
        $newTemplate->json_content = $chosenTemplate->json_content;
        $newTemplate->timecreated = time();
        $newTemplate->timemodified = time();

        $DB->insert_record('h5plib_poc_editor_template', $newTemplate);
        redirect(new moodle_url('/h5p/h5plib/poc_editor/configuration.php'), get_string('templateadded', 'h5plib_poc_editor'), null,
                \core\output\notification::NOTIFY_SUCCESS);
    }
}

if ($data = $updateTemplateForm->get_data()) {
    $templates = h5p_poc_editor_get_updatable_templates();
    $isSuccess = h5p_poc_editor_update_templates($templates);
    if ($isSuccess) {
        redirect(new moodle_url('/h5p/h5plib/poc_editor/configuration.php'), get_string('templatesupdated', 'h5plib_poc_editor'),
                null, \core\output\notification::NOTIFY_SUCCESS);
    }
}

if ($data = $deleteTemplateForm->get_data()) {
    if (isset($data->select_delete_template)) {
        $selectedTemplateIndex = $data->select_delete_template;
        $selectedTemplate = h5p_poc_editor_find_template($selectedTemplateIndex);
        $DB->delete_records('h5plib_poc_editor_template', ['id' => $selectedTemplate->id]);
        redirect(new moodle_url('/h5p/h5plib/poc_editor/configuration.php'), get_string('templatedeleted', 'h5plib_poc_editor'),
                null, \core\output\notification::NOTIFY_SUCCESS);
    }
}

echo $OUTPUT->header();
echo html_writer::tag('br', '');
echo html_writer::tag('a', get_string('back', 'h5plib_poc_editor'), h5plib_poc_editor_get_back_btn_attributes());
$configForm->display();
echo html_writer::tag('br', '');
echo html_writer::tag('h3', get_string('templatemanagementtitle', 'h5plib_poc_editor'));
$addTemplateForm->display();
echo html_writer::tag('br', '');
$updateTemplateForm->display();
echo html_writer::tag('br', '');
$deleteTemplateForm->display();
echo $OUTPUT->footer();

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
require_once($CFG->dirroot . '/course/classes/category.php');
require_once($CFG->dirroot.'/course/lib.php');
require_once('./lib.php');
require_login();

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/h5p/h5plib/poc_editor/configuration.php'));
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('pluginname', 'h5plib_poc_editor') . " " . $SITE->fullname);
$PAGE->set_heading(get_string('configtitle', 'h5plib_poc_editor'));

if (!is_siteadmin()) {
    redirect(new moodle_url('/h5p/h5plib/poc_editor/'), 'Unable to access to the plugin\'s settings page. You need to be a site administrator.', null, \core\output\notification::NOTIFY_ERROR);
}

$debugvar = "";

$configform = new \h5plib_poc_editor\form\config_form();
$addtemplateform = new \h5plib_poc_editor\form\config_add_template_form();
$updatetemplateform = new \h5plib_poc_editor\form\config_update_template_form();
$deletetemplateform = new \h5plib_poc_editor\form\config_delete_template_form();

if ($data = $configform->get_data()){

    $categoryname = "Poc Editor";
    $dbcategory = $DB->get_record('course_categories', ["name" => $categoryname]);
    $categoryid = 0;
    if (!$dbcategory) {
        $newcategory = new stdClass();
        $newcategory->name = $categoryname;
        $newcategory->description = "A category to reference all the courses needed for the presentation editor";
        $newcategory->visible = 0;
        $newcategory->idnumber = '';

        $createdcategory = core_course_category::create($newcategory);
        $categoryid = $createdcategory->id;
    }
    else {
        $categoryid = $dbcategory->id;
    }

    $newcourse = new stdClass();
    $newcourse->shortname = "poceditor";
    $newcourse->fullname = "Poc Editor";
    $newcourse->category = $categoryid;
    $newcourse->visible = 0;

    $createdcourse = create_course($newcourse);
    redirect(new moodle_url('/h5p/h5plib/poc_editor/configuration.php'), 'Poc Editor course added successfully', null, \core\output\notification::NOTIFY_SUCCESS);
}

if ($data = $addtemplateform->get_data()) {

    if (isset($data->available_templates)) {
        $templateindex = $data->available_templates;
        $templatecourseid = h5p_poc_editor_get_template_course()->id;
        $addedtemplates = h5p_poc_editor_get_added_templates(); 
        $availabletemplates = h5p_poc_editor_get_available_templates($addedtemplates, $templatecourseid);
        
        $chosentemplate = $availabletemplates[$templateindex];
        
        $newtemplate = new stdClass();
        $newtemplate->presentationid = $chosentemplate->id;
        $newtemplate->json_content = $chosentemplate->json_content;
        $newtemplate->timecreated = time();
        $newtemplate->timemodified = time();

        $DB->insert_record('h5plib_poc_editor_template', $newtemplate);
        redirect(new moodle_url('/h5p/h5plib/poc_editor/configuration.php'), 'Template added successfully', null, \core\output\notification::NOTIFY_SUCCESS);
    }
}

if ($data = $updatetemplateform->get_data()) {
    $templates = h5p_poc_editor_get_updatable_templates();
    $issuccess = h5p_poc_editor_update_templates($templates);
    if ($issuccess) {
        redirect(new moodle_url('/h5p/h5plib/poc_editor/configuration.php'), 'Templates updated successfully', null, \core\output\notification::NOTIFY_SUCCESS);
    }
}

if ($data = $deletetemplateform->get_data()) {
    if (isset($data->select_delete_template)) {
        $selectedtemplateindex = $data->select_delete_template;
        $selectedtemplate = h5p_poc_editor_find_template($selectedtemplateindex);
        $DB->delete_records('h5plib_poc_editor_template', ['id' => $selectedtemplate->id]);
        redirect(new moodle_url('/h5p/h5plib/poc_editor/configuration.php'), 'Template deleted successfully', null, \core\output\notification::NOTIFY_SUCCESS);
    }
}

echo $OUTPUT->header();
$settings_url = new moodle_url('/h5p/h5plib/poc_editor/');
echo html_writer::tag('a', 'Back', ['href' => $settings_url , 'role' => 'button','class' => 'btn btn-primary btn-sm', 'data-bs-toggle' => 'button', 'aria-pressed' => 'true', 'style' => ' background-color: #3F2A56; padding:6px 15px 6px 15px; margin-top: 10px; border-color: #3F2A56;']);
$configform->display();
echo html_writer::tag('h3', get_string('templatemanagementtitle', 'h5plib_poc_editor'));
$addtemplateform->display();
$updatetemplateform->display();
$deletetemplateform->display();
print_r($debugvar);
echo $OUTPUT->footer();

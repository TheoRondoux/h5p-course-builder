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

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/h5p/h5plib/poc_editor/configuration.php'));
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('pluginname', 'h5plib_poc_editor') . " " . $SITE->fullname);
$PAGE->set_heading(get_string('configtitle', 'h5plib_poc_editor'));

$debugvar = "";

$configform = new \h5plib_poc_editor\form\config_form();
$addtemplateform = new \h5plib_poc_editor\form\config_add_template_form();

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
        $categoryid->$createdcategory->id;
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
}

echo $OUTPUT->header();
$configform->display();
$addtemplateform->display();
echo $debugvar;
echo $OUTPUT->footer();

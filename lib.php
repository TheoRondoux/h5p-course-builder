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
 * @package     h5plib_poc_editor
 * @category    string
 * @copyright   2024 - Théo Rondoux
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

function h5p_poc_editor_get_courses() {
    global $DB;
    $courses = [];
    $retrievedcourses = $DB->get_records('course');
    foreach($retrievedcourses as $course) {
        if ($course->id > 1) {
            array_push($courses, $course);
        }
    }
    return $courses;
}

function h5p_poc_editor_find_course($selectedcourseindex, $courses){
    return $courses[($selectedcourseindex - 1)];
}

function h5p_poc_editor_generate_slug($title) {
    $slug = str_replace([' ', '(', ')', 'é', 'è', 'à', 'ç', 'ù'], ['-', '', '', 'e', 'e', 'a', 'c', 'u'], $title);
    return $slug;
}

function h5p_poc_editor_get_template_course() {
    global $DB;
    $templatecourse = $DB->get_record('course', ['shortname' => 'poceditor']);
    return $templatecourse;
}

function h5p_poc_editor_get_added_templates() {
    global $DB;
    $addedtemplates = $DB->get_records('h5plib_poc_editor_template');
    return $addedtemplates;
}

function h5p_poc_editor_get_available_templates($addedtemplates, $templatecourseid) {
    global $DB;
    $availabletemplates = [];
    $importedtemplates = $DB->get_records('hvp', ['course' => $templatecourseid]);
    if ($addedtemplates) {
        foreach ($importedtemplates as $importedtemplate) {
            $added = false;
            foreach ($addedtemplates as $addedtemplate) {
                if ($addedtemplate->presentationid == $importedtemplate->id) {
                    $added = true;
                }
            }
            if (!$added) {
                array_push($availabletemplates, $importedtemplate);
            }
        }
    }
    else {
        foreach ($importedtemplates as $importedtemplate) {
            array_push($availabletemplates, $importedtemplate);
        }
    }

    return $availabletemplates;
}

function h5p_poc_editor_get_updatable_templates() {
    global $DB;
    $updatabletemplates = $DB->get_records_sql('SELECT * FROM mdl_hvp WHERE id IN (SELECT presentationid FROM mdl_h5plib_poc_editor_template WHERE mdl_h5plib_poc_editor_template.timemodified != mdl_hvp.timemodified)');
    return $updatabletemplates;
}

function h5p_poc_editor_find_template($index) {
    global $DB;
    $templateinfos = new stdClass();
    
    $templates = $DB->get_records('h5plib_poc_editor_template');
    $retrieved_selected_template = $templates[($index + 1)];

    $retrieved_hvp_template = $DB->get_record('hvp', ['id' => $retrieved_selected_template->presentationid]);
    
    $templateinfos->json_content = $retrieved_hvp_template->json_content;
    
    $templatelib = $DB->get_record('hvp_libraries', ['id' => $retrieved_hvp_template->main_library_id]);
    
    $templatelibdesc = $templatelib->machine_name . ' ' . $templatelib->major_version . '.' . $templatelib->minor_version;
    
    $templateinfos->library = $templatelibdesc;
    
    return $templateinfos;
}

function h5p_poc_editor_update_templates($templates) {
    global $DB;
    if (!empty($templates)) {
        foreach ($templates as $template) {
            $templateid = $DB->get_record_sql("SELECT id FROM mdl_h5plib_poc_editor_template WHERE presentationid = ". $template->id);
            
            $dataToUpdate = new stdClass();
            $dataToUpdate->id = $templateid->id;
            $dataToUpdate->json_content = $template->json_content;
            $dataToUpdate->timemodified = $template->timemodified;
            
            $success = $DB->update_record('h5plib_poc_editor_template', $dataToUpdate);
            if (!$success) {
                return false;
            }
        }
        return true;
    }
}
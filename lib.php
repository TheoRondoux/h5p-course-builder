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

function h5plib_poc_editor_extend_navigation_frontpage(navigation_node $frontpage) {
    $frontpage->add(
            get_string('pluginname', 'h5plib_poc_editor'),
            new moodle_url('/h5p/h5plib/poc_editor/index.php'),
            navigation_node::TYPE_CUSTOM,
            'poceditor',
            2
    );
}

function h5p_poc_editor_get_courses(): array {
    global $DB;
    $courses = [];
    $retrievedcourses = $DB->get_records('course');
    foreach ($retrievedcourses as $course) {
        if ($course->id > 1 && $course->shortname != 'poceditor') {
            $courses[] = $course;
        }
    }
    return $courses;
}

/**
 * @param int $selectedcourseindex The index of the course in the select HTML elem
 * @param array $courses All the courses
 *
 * @return stdClass The wanted course
 */
function h5p_poc_editor_find_course(int $selectedcourseindex, array $courses): stdClass {
    return $courses[($selectedcourseindex - 1)];
}

/**
 * @return stdClass The course where templates can be added
 */
function h5p_poc_editor_get_template_course(): stdClass {
    global $DB;
    return $DB->get_record('course', ['shortname' => 'poceditor']);
}

/**
 * @return array All the templates usable for creating templates
 * */
function h5p_poc_editor_get_added_templates(): array {
    global $DB;
    return $DB->get_records('h5plib_poc_editor_template');
}

function h5p_poc_editor_get_available_templates(array $addedTemplates, int $templateCourseId): array {
    global $DB;
    $availableTemplates = [];
    $importedTemplates = $DB->get_records('hvp', ['course' => $templateCourseId]);
    if ($addedTemplates) {
        foreach ($importedTemplates as $importedTemplate) {
            $added = false;
            foreach ($addedTemplates as $addedTemplate) {
                if ($addedTemplate->presentationid == $importedTemplate->id) {
                    $added = true;
                }
            }
            if (!$added) {
                $availableTemplates[] = $importedTemplate;
            }
        }
    } else {
        foreach ($importedTemplates as $importedTemplate) {
            $availableTemplates[] = $importedTemplate;
        }
    }

    return $availableTemplates;
}

/**
 * @return array The templates that have been updated in the course but not in the plugin
 */
function h5p_poc_editor_get_updatable_templates(): array {
    global $DB;
    return $DB->get_records_sql('SELECT * FROM mdl_hvp WHERE id IN (SELECT presentationid FROM mdl_h5plib_poc_editor_template WHERE mdl_h5plib_poc_editor_template.timemodified < mdl_hvp.timemodified)');
}

function h5p_poc_editor_find_template(int $index): stdClass {
    global $DB;
    $templateInfos = new stdClass();

    $result = $DB->get_records('h5plib_poc_editor_template');
    $templates = [];
    foreach ($result as $template) {
        $templates[] = $template;
    }

    $retrieved_selected_template = $templates[($index)];
    $retrieved_hvp_template = $DB->get_record('hvp', ['id' => $retrieved_selected_template->presentationid]);

    $templateInfos->json_content = $retrieved_hvp_template->json_content;

    $templateLib = $DB->get_record('hvp_libraries', ['id' => $retrieved_hvp_template->main_library_id]);

    $templateLibDesc = $templateLib->machine_name . ' ' . $templateLib->major_version . '.' . $templateLib->minor_version;

    $templateInfos->library = $templateLibDesc;
    $templateInfos->id = $retrieved_selected_template->id;

    return $templateInfos;
}

function h5p_poc_editor_update_templates(array $templates): bool {
    global $DB;
    if (!empty($templates)) {
        foreach ($templates as $template) {
            $templateId =
                    $DB->get_record_sql("SELECT id FROM mdl_h5plib_poc_editor_template WHERE presentationid = " . $template->id);
            $dataToUpdate = new stdClass();
            $dataToUpdate->id = $templateId->id;
            $dataToUpdate->json_content = $template->json_content;
            $dataToUpdate->timemodified = $template->timemodified;

            $success = $DB->update_record('h5plib_poc_editor_template', $dataToUpdate);
            if (!$success) {
                return false;
            }
        }
        return true;
    }
    return false;
}

function h5p_poc_editor_get_templates_names(array $templates): array {
    global $DB;
    $names = [];
    foreach ($templates as $template) {
        $templateRecord = $DB->get_record('hvp', ['id' => $template->presentationid]);
        if (!empty($templateRecord)) {
            $names[] = $templateRecord->name;
        }
    }

    return $names;
}

function h5plib_poc_editor_display_all_presentations(array $presentations): void {
    global $OUTPUT;
    global $DB;

    echo $OUTPUT->box_start('card-columns');
    echo html_writer::start_tag('div', ['class' => 'user-pres']);
    foreach ($presentations as $presentation) {
        $moduleid = $DB->get_record('course_modules', ['instance' => $presentation->id])->id;
        $courseViewUrl =
                '<a href="' . new moodle_url("/mod/hvp/view.php?id=" . $moduleid . "&forceview=1") . '">' . $presentation->name .
                '</a>';
        echo html_writer::start_tag('div', ['class' => 'card']);
        echo html_writer::start_tag('div', ['class' => 'card-body']);
        echo html_writer::tag('p', $courseViewUrl, ['class' => 'card-text']);
        if ($presentation->shared == 1) {
            echo html_writer::start_tag('center');
            echo html_writer::tag('small', 'Shared', ['class' => 'text-muted']);
            echo html_writer::end_tag('center');
        }
        echo html_writer::start_tag('p', ['class' => 'card-text']);
        echo html_writer::tag('small', userdate($presentation->timecreated), ['class' => 'text-muted']);
        echo html_writer::end_tag('p');
        echo html_writer::end_tag('div');
        echo html_writer::end_tag('div');
    }
    echo html_writer::end_tag('div');
    echo $OUTPUT->box_end();
}

function h5plib_poc_editor_display_some_presentations(array $presentations, int $number = 5): void {
    global $OUTPUT;
    global $DB;

    echo $OUTPUT->box_start('card-columns');
    echo html_writer::start_tag('div', ['class' => 'user-pres']);
    for ($i = 0; $i < $number; $i++) {
        $presentationsarray = [];
        foreach ($presentations as $pres) {
            array_push($presentationsarray, $pres);
        }
        $presentation = $presentationsarray[$i];
        $moduleId = $DB->get_record('course_modules', ['instance' => $presentation->id])->id;
        $courseViewUrl =
                '<a href="' . new moodle_url("/mod/hvp/view.php?id=" . $moduleId . "&forceview=1") . '">' . $presentation->name .
                '</a>';
        echo html_writer::start_tag('div', ['class' => 'card']);
        echo html_writer::start_tag('div', ['class' => 'card-body']);
        echo html_writer::tag('p', $courseViewUrl, ['class' => 'card-text']);
        if ($presentation->shared == 1) {
            echo html_writer::start_tag('center');
            echo html_writer::tag('small', 'Shared', ['class' => 'text-muted']);
            echo html_writer::end_tag('center');
        }
        echo html_writer::start_tag('p', ['class' => 'card-text']);
        echo html_writer::tag('small', userdate($presentation->timecreated), ['class' => 'text-muted']);
        echo html_writer::end_tag('p');
        echo html_writer::end_tag('div');
        echo html_writer::end_tag('div');
    }
    echo html_writer::end_tag('div');
    echo $OUTPUT->box_end();
}

function h5plib_poc_editor_generate_module(string $title, string $template, string $introduction, string $modulename): stdClass {
    global $DB;
    $retrievedModule = $DB->get_record('modules', ['name' => $modulename]);
    if (empty($retrievedModule)) {
        throw new ErrorException('The module "' . $modulename . '" does not exist.');
    }

    $newModule = new stdClass();
    $newModule->module = $retrievedModule->id;
    $newModule->visible = 1;
    $newModule->visibleoncoursepage = 1;
    $newModule->instance = 0;
    $newModule->section = 3;
    $newModule->modulename = $modulename;
    $newModule->name = $title;
    $newModule->introformat = 1;
    $newModule->params = $template->json_content;
    $newModule->h5plibrary = $template->library;
    $newModule->metadata = "";
    $newModule->intro = $introduction;
    $newModule->cmidnumber = 0;
    $newModule->h5paction = 'create';

    return $newModule;
}

/**
 * @param $user stdClass The user to check the role
 * @return array an array of course ids
 * */
function h5plib_poc_editor_check_if_teacher_in_courses(stdClass $user, array $courses): array {
    $user_id = $user->id;
    $capability = 'moodle/course:update';
    $modifiable_courses = [];
    foreach ($courses as $course) {
        if (has_capability($capability, context_course::instance($course->id), $user_id)) {
            $modifiable_courses[] = $course;
        }
    }
    return $modifiable_courses;
}

/**
 * Custom error redirection according to the plugin needs
 *
 * @param string $message
 * @param string $path
 * @return void
 */
function h5plib_poc_editor_redirect_error(string $message, string $path = '/h5p/h5plib/poc_editor') {
    $prefix = "[" . get_string('pluginname', 'h5plib_poc_editor') . "] Error: ";
    redirect(new moodle_url($path),
            $prefix . $message,
            null,
            \core\output\notification::NOTIFY_ERROR);

}

/**
 * Custom success redirection according to the plugin needs
 *
 * @param string $message
 * @return void
 */
function h5plib_poc_editor_redirect_success(string $message): void {
    redirect(new moodle_url('/h5p/h5plib/poc_editor'),
            $message,
            null,
            \core\output\notification::NOTIFY_SUCCESS);
}

function h5plib_poc_editor_is_enrolled_to_any_course($user): bool {
    $courses = h5p_poc_editor_get_courses();
    $isEnrolled = false;
    foreach ($courses as $course) {
        if (is_enrolled(context_course::instance($course->id), $user)) {
            $isEnrolled = true;
        }

    }
    return $isEnrolled;
}

/**
 * Redirects the user if not enrolled in any course of if not editing teacher for any course
 *
 * @param stdClass $user
 * @return void
 */
function h5plib_poc_editor_no_access_redirect(stdClass $user): void {
    if (!is_siteadmin() && !h5plib_poc_editor_is_enrolled_to_any_course($user)) {
        h5plib_poc_editor_redirect_error(get_string('nocoursesenrolledin', 'h5plib_poc_editor'), '/');
    }

    $courses = h5p_poc_editor_get_courses();
    $teacherCourses = h5plib_poc_editor_check_if_teacher_in_courses($user, $courses);
    if (!is_siteadmin() && sizeof($teacherCourses) < 1) {
        h5plib_poc_editor_redirect_error(get_string('noteditingteacherinanycourse', 'h5plib_poc_editor'), '/');
    }
}
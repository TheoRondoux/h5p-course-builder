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

function h5p_poc_editor_get_courses(): array {
    global $DB;
    $courses = [];
    $retrievedcourses = $DB->get_records('course');
    foreach ($retrievedcourses as $course) {
        if ($course->id > 1 && $course->shortname != 'poceditor') {
            array_push($courses, $course);
        }
    }
    return $courses;
}

function h5p_poc_editor_find_course($selectedcourseindex, $courses): stdClass {
    return $courses[($selectedcourseindex - 1)];
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

function h5p_poc_editor_get_available_templates($addedtemplates, $templatecourseid): array {
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
    } else {
        foreach ($importedtemplates as $importedtemplate) {
            array_push($availabletemplates, $importedtemplate);
        }
    }

    return $availabletemplates;
}

function h5p_poc_editor_get_updatable_templates(): array {
    global $DB;
    return $DB->get_records_sql('SELECT * FROM mdl_hvp WHERE id IN (SELECT presentationid FROM mdl_h5plib_poc_editor_template WHERE mdl_h5plib_poc_editor_template.timemodified < mdl_hvp.timemodified)');
}

function h5p_poc_editor_find_template($index): stdClass {
    global $DB;
    $templateinfos = new stdClass();

    $gottemplates = $DB->get_records('h5plib_poc_editor_template');
    $templates = [];
    foreach ($gottemplates as $gottemplate) {
        array_push($templates, $gottemplate);
    }

    $retrieved_selected_template = $templates[($index)];

    $retrieved_hvp_template = $DB->get_record('hvp', ['id' => $retrieved_selected_template->presentationid]);

    $templateinfos->json_content = $retrieved_hvp_template->json_content;

    $templatelib = $DB->get_record('hvp_libraries', ['id' => $retrieved_hvp_template->main_library_id]);

    $templatelibdesc = $templatelib->machine_name . ' ' . $templatelib->major_version . '.' . $templatelib->minor_version;

    $templateinfos->library = $templatelibdesc;
    $templateinfos->id = $retrieved_selected_template->id;

    return $templateinfos;
}

function h5p_poc_editor_update_templates($templates): bool {
    global $DB;
    if (!empty($templates)) {
        foreach ($templates as $template) {
            $templateid =
                    $DB->get_record_sql("SELECT id FROM mdl_h5plib_poc_editor_template WHERE presentationid = " . $template->id);

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
    return false;
}

function h5p_poc_editor_get_templates_names($templates): array {
    global $DB;
    $names = [];
    foreach ($templates as $template) {
        $templaterecord = $DB->get_record('hvp', ['id' => $template->presentationid]);
        if (!empty($templaterecord)) {
            array_push($names, $templaterecord->name);
        }
    }

    return $names;
}

function h5plib_poc_editor_display_all_presentations($presentations): void {
    global $OUTPUT;
    global $DB;

    echo $OUTPUT->box_start('card-columns');
    echo html_writer::start_tag('div', ['class' => 'user-pres']);
    foreach ($presentations as $p) {
        $moduleid = $DB->get_record('course_modules', ['instance' => $p->id])->id;
        $courseviewurl =
                '<a href="' . new moodle_url("/mod/hvp/view.php?id=" . $moduleid . "&forceview=1") . '">' . $p->name . '</a>';
        $courseediturl = '<a href="' . new moodle_url("/course/modedit.php?update=" . $moduleid . "&return=1") . '">[Edit]</a>';
        echo html_writer::start_tag('div', ['class' => 'card']);
        echo html_writer::start_tag('div', ['class' => 'card-body']);
        echo html_writer::tag('p', $courseviewurl, ['class' => 'card-text']);
        if ($p->shared == 1) {
            echo html_writer::start_tag('center');
            echo html_writer::tag('small', 'Shared', ['class' => 'text-muted']);
            echo html_writer::end_tag('center');
        }
        echo html_writer::start_tag('p', ['class' => 'card-text']);
        echo html_writer::tag('small', userdate($p->timecreated), ['class' => 'text-muted']);
        echo html_writer::end_tag('p');
        echo html_writer::end_tag('div');
        echo html_writer::end_tag('div');
    }
    echo html_writer::end_tag('div');
    echo $OUTPUT->box_end();
}

function h5plib_poc_editor_display_some_presentations($presentations, $number = 5): void {
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
        $moduleid = $DB->get_record('course_modules', ['instance' => $presentation->id])->id;
        $courseviewurl =
                '<a href="' . new moodle_url("/mod/hvp/view.php?id=" . $moduleid . "&forceview=1") . '">' . $presentation->name .
                '</a>';
        $courseediturl = '<a href="' . new moodle_url("/course/modedit.php?update=" . $moduleid . "&return=1") . '">[Edit]</a>';
        echo html_writer::start_tag('div', ['class' => 'card']);
        echo html_writer::start_tag('div', ['class' => 'card-body']);
        echo html_writer::tag('p', $courseviewurl, ['class' => 'card-text']);
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

function h5plib_poc_editor_generate_module($title, $template, $introduction, $modulename): stdClass {
    global $DB;
    $retrivedmodule = $DB->get_record('modules', ['name' => $modulename]);
    if (empty($retrivedmodule)) {
        throw new ErrorException('The module "' . $modulename . '" does not exist.');
    }

    $newmodule = new stdClass();
    $newmodule->module = $retrivedmodule->id;
    $newmodule->visible = 1;
    $newmodule->visibleoncoursepage = 1;
    $newmodule->instance = 0;
    $newmodule->section = 3;
    $newmodule->modulename = $modulename;
    $newmodule->name = $title;
    $newmodule->introformat = 1;
    $newmodule->params = $template->json_content;
    $newmodule->h5plibrary = $template->library;
    $newmodule->metadata = "";
    $newmodule->intro = $introduction;
    $newmodule->cmidnumber = 0;
    $newmodule->h5paction = 'create';

    return $newmodule;
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

function h5plib_poc_editor_redirect_error(string $message) {
    redirect(new moodle_url('/h5p/h5plib/poc_editor'),
            $message,
            null,
            \core\output\notification::NOTIFY_ERROR);

}

function h5plib_poc_editor_redirect_success(string $message) {
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
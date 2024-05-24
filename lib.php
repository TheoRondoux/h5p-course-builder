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
 * @package     h5plib_course_builder
 * @category    string
 * @copyright   2024 - Théo Rondoux & Godfred Boaheng
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

/**
 * Used to display a button on the frontpage of the plugin
 *
 * @param navigation_node $frontpage
 * @return void
 * @throws coding_exception
 */
function h5plib_course_builder_extend_navigation_frontpage(navigation_node $frontpage) {
    $frontpage->add(
            get_string('pluginname', 'h5plib_course_builder'),
            new moodle_url('/h5p/h5plib/course_builder/index.php'),
            navigation_node::TYPE_CUSTOM,
            'coursebuilder',
            2
    );
}

/**
 * Retrieve the courses in the database.
 *
 * @return array
 * @throws dml_exception
 */
function h5p_course_builder_get_courses(): array {
    global $DB;
    $courses = [];
    $retrievedcourses = $DB->get_records('course');
    foreach ($retrievedcourses as $course) {
        if ($course->id > 1 && $course->shortname != 'coursebuilder') {
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
function h5p_course_builder_find_course(int $selectedcourseindex, array $courses): stdClass {
    return $courses[($selectedcourseindex - 1)];
}

/**
 * @return stdClass The course where templates can be added
 */
function h5p_course_builder_get_template_course(): bool|stdClass {
    global $DB;
    return $DB->get_record('course', ['shortname' => 'coursebuilder']);
}

/**
 * @return array All the templates usable for creating templates
 * */
function h5p_course_builder_get_added_templates(): array {
    global $DB;
    return $DB->get_records('h5plib_course_builder_template');
}

/**
 * Retrieve the templates added by admins in the plugin
 *
 * @param array $addedTemplates
 * @param int $templateCourseId
 * @return array
 * @throws dml_exception
 */
function h5p_course_builder_get_available_templates(array $addedTemplates, int $templateCourseId): array {
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
 * Get the templates modified recently
 *
 * @return array The templates that have been updated in the course but not in the plugin
 */
function h5p_course_builder_get_updatable_templates(): array {
    global $DB;
    return $DB->get_records_sql('SELECT * FROM mdl_hvp WHERE id IN (SELECT presentationid FROM mdl_h5plib_course_builder_template WHERE mdl_h5plib_course_builder_template.timemodified < mdl_hvp.timemodified)');
}

/**
 * Find a template depending on its index in the templates list
 *
 * @param int $index
 * @return stdClass
 * @throws dml_exception
 */
function h5p_course_builder_find_template(int $index): stdClass {
    global $DB;
    $templateInfos = new stdClass();

    $result = $DB->get_records('h5plib_course_builder_template');
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

/**
 * Updates the templates in the database
 *
 * @param array $templates
 * @return bool
 * @throws dml_exception
 */
function h5p_course_builder_update_templates(array $templates): bool {
    global $DB;
    if (!empty($templates)) {
        foreach ($templates as $template) {
            $templateId =
                    $DB->get_record_sql("SELECT id FROM mdl_h5plib_course_builder_template WHERE presentationid = " .
                            $template->id);
            $dataToUpdate = new stdClass();
            $dataToUpdate->id = $templateId->id;
            $dataToUpdate->json_content = $template->json_content;
            $dataToUpdate->timemodified = $template->timemodified;

            $success = $DB->update_record('h5plib_course_builder_template', $dataToUpdate);
            if (!$success) {
                return false;
            }
        }
        return true;
    }
    return false;
}

/**
 * Gives a list of the templates names
 *
 * @param array $templates
 * @return array
 * @throws dml_exception
 */
function h5p_course_builder_get_templates_names(array $templates): array {
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

/**
 * Used to display all the presentations in a list
 *
 * @param array $presentations
 * @param stdClass $user
 * @return void
 */
function h5plib_course_builder_display_all_presentations(array $presentations, stdClass $user): void {
    global $OUTPUT;
    global $DB;

    echo $OUTPUT->box_start('card-columns');
    foreach ($presentations as $presentation) {
        $relatedCourse = $DB->get_record('course', ['id' => $presentation->course]);
        $detailsUrl =
                '<a href="' . new moodle_url("/h5p/h5plib/course_builder/details.php", ['id' => $presentation->id]) . '">' .
                $presentation->name .
                '</a>';
        echo html_writer::start_tag('div', ['class' => 'card']);
        echo html_writer::empty_tag('img',
                ['src' => 'https://images.unsplash.com/photo-1517760444937-f6397edcbbcd?ixlib=rb-0.3.5&q=80&fm=jpg&crop=entropy&cs=tinysrgb&w=1080&fit=max&ixid=eyJhcHBfaWQiOjMyMDc0fQ&s=42b2d9ae6feb9c4ff98b9133addfb698',
                        'class' => 'card-img-top', 'alt' => 'Card image']);
        echo html_writer::start_tag('div', ['class' => 'card-body']);
        echo html_writer::tag('h5', $detailsUrl, ['class' => 'card-title']);
        echo html_writer::tag('p', get_string('partofcourse', 'h5plib_course_builder') . ' ' . $relatedCourse->fullname , ['class' => 'card-text']);
        if ($presentation->shared == 1 && $user->id == $presentation->userid) {
            echo html_writer::tag('small', 'Shared', ['class' => 'text-muted']);
        } else if ($presentation->shared == 1) {
            echo html_writer::tag('small', 'By ' . $presentation->firstname . ' ' . $presentation->lastname,
                    ['class' => 'text-muted']);
        }
        echo html_writer::end_tag('div');

        echo html_writer::start_tag('div', ['class' => 'card-footer']);
        echo html_writer::tag('small', date('l d M, Y', $presentation->timecreated), ['class' => 'text-muted']);
        echo html_writer::end_tag('div');
        echo html_writer::end_tag('div'); // card
    }
    echo $OUTPUT->box_end();
}

/**
 * Used to display some presentations in a carousel
 *
 * @param array $presentations
 * @param int $number
 * @param stdClass $user
 * @return void
 */
function h5plib_course_builder_display_some_presentations(array $presentations, stdClass $user, int $number = 6): void {

    echo html_writer::start_tag('section', ['class' => 'pt-5 pb-5']);
    echo html_writer::start_tag('div', ['class' => 'container']);
    echo html_writer::start_tag('div', ['class' => 'row']);
    echo h5plib_course_builder_generate_carousel($presentations, $number, $user);
    echo html_writer::end_tag('div'); // row
    echo html_writer::end_tag('div'); // container
    echo html_writer::end_tag('section');
}

/**
 * Generates a carousel for a certain amount of presentations
 *
 * @param array $presentations
 * @param int $number
 * @param stdClass $user
 * @return void
 */
function h5plib_course_builder_generate_carousel(array $presentations, int $number = 6, stdClass $user): void {
    echo html_writer::start_tag('div', ['class' => 'col-12']);
    echo html_writer::start_tag('div',
            ['id' => 'carouselExampleIndicators', 'class' => 'carousel slide', 'data-bs-interval' => 'false',
                    'data-interval' => 'false']);
    echo html_writer::start_tag('div', ['class' => 'carousel-inner']);

    for ($i = 0; $i < $number; $i += 3) {
        echo h5plib_course_builder_generate_presentation_card($presentations, $i, $user);
    }
    echo html_writer::end_tag('div'); // carousel-inner
    echo html_writer::end_tag('div'); // carousel slide
    echo html_writer::end_tag('div'); // col-12
}

/**
 * Used to generate a card for some presentations, depending on the index
 *
 * @param array $presentations
 * @param int $startIndex
 * @param stdClass $user
 * @return void
 */
function h5plib_course_builder_generate_presentation_card(array $presentations, int $startIndex, stdClass $user): void {
    global $DB;

    $presentationsarray = [];
    foreach ($presentations as $pres) {
        array_push($presentationsarray, $pres);
    }
    echo html_writer::start_tag('div', ['class' => 'carousel-item' . ($startIndex === 0 ? ' active' : '')]);
    echo html_writer::start_tag('div', ['class' => 'row']);
    for ($j = $startIndex; $j < $startIndex + 3; $j++) {
        if (isset($presentationsarray[$j])) {
            $presentation = $presentationsarray[$j];
            $moduleid[$j] = $DB->get_record('course_modules', ['instance' => $presentation->id])->id;
            $courseviewurl[$j] =
                    '<a href="' . new moodle_url("details.php", ['id' => $presentation->id]) . '">' . $presentation->name . '</a>';

            echo html_writer::start_tag('div', ['class' => 'col-md-4 md-3']);
            h5plib_course_builder_generate_presentation_content($presentation, $courseviewurl[$j], $user);
            echo html_writer::end_tag('div'); // col-md-4 md-3
        }
    }
    echo html_writer::end_tag('div'); // row
    echo html_writer::end_tag('div'); // carousel-item
}

/**
 * Used to generate the content of a presentation,
 *
 * @param stdClass $presentations
 * @param string $courseviewurl
 * @param stdClass $user
 * @return void
 */
function h5plib_course_builder_generate_presentation_content(stdClass $presentation, string $courseviewurl, stdClass $user): void {
    global $DB;
    $relatedCourse = $DB->get_record('course', ['id' => $presentation->course]);

    echo html_writer::start_tag('div', ['class' => 'card']);
    echo html_writer::empty_tag('img',
            ['src' => 'https://images.unsplash.com/photo-1517760444937-f6397edcbbcd?ixlib=rb-0.3.5&q=80&fm=jpg&crop=entropy&cs=tinysrgb&w=1080&fit=max&ixid=eyJhcHBfaWQiOjMyMDc0fQ&s=42b2d9ae6feb9c4ff98b9133addfb698',
                    'class' => 'card-img-top', 'alt' => 'Card image']);
    echo html_writer::start_tag('div', ['class' => 'card-body']);
    echo html_writer::tag('h5', $courseviewurl, ['class' => 'card-title']);
    echo html_writer::tag('p', get_string('partofcourse', 'h5plib_course_builder') . ' ' . $relatedCourse->fullname, ['class' => 'card-text']);
    if ($presentation->shared == 1 && $user->id == $presentation->userid) {
        echo html_writer::start_tag('center');
        echo html_writer::tag('small', 'Shared', ['class' => 'text-muted']);
        echo html_writer::end_tag('center');
    } else if ($presentation->shared == 1) {
        echo html_writer::tag('small', 'By ' . $presentation->firstname . ' ' . $presentation->lastname, ['class' => 'text-muted']);
    }
    echo html_writer::end_tag('div'); // card-body
    echo html_writer::start_tag('div', ['class' => 'card-footer']);
    echo html_writer::tag('small', date('l d M, Y', $presentation->timecreated), ['class' => 'text-muted']);
    echo html_writer::end_tag('div'); // card-footer
    echo html_writer::end_tag('div'); // card
}

/**
 * Displays a card on the screen with some information about a presentation
 *
 * @param mixed $presentation
 * @param stdClass $user
 * @return void
 * @throws moodle_exception
 */
function h5plib_course_builder_display_card_from_presentation(mixed $presentation, stdClass $user): void {
    $detailsUrl =
            '<a href="' . new moodle_url("/h5p/h5plib/course_builder/details.php", ['id' => $presentation->id]) . '">' .
            $presentation->name .
            '</a>';
    echo html_writer::start_tag('div', ['class' => 'card']);
    echo html_writer::start_tag('div', ['class' => 'card-body']);
    echo html_writer::tag('p', $detailsUrl, ['class' => 'card-text']);
    if ($presentation->shared == 1 && $user->id == $presentation->userid) {
        echo html_writer::start_tag('center');
        echo html_writer::tag('small', 'Shared', ['class' => 'text-muted']);
        echo html_writer::end_tag('center');
    } else if ($presentation->shared == 1) {
        echo html_writer::tag('small', 'By ' . $presentation->firstname . ' ' . $presentation->lastname, ['class' => 'text-muted']);
    }
    echo html_writer::start_tag('p', ['class' => 'card-text']);
    echo html_writer::tag('small', userdate($presentation->timecreated), ['class' => 'text-muted']);
    echo html_writer::end_tag('p');
    echo html_writer::end_tag('div');
    echo html_writer::end_tag('div');
}

/**
 * @param $title
 * @param $template
 * @param $introduction
 * @param $modulename
 * @return stdClass
 */
function h5plib_course_builder_generate_module($title, $template, $introduction, $modulename) {
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
function h5plib_course_builder_check_if_teacher_in_courses(stdClass $user, array $courses): array {
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
function h5plib_course_builder_redirect_error(string $message, string $path = '/h5p/h5plib/course_builder') {
    $prefix = "[" . get_string('pluginname', 'h5plib_course_builder') . "] Error: ";
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
function h5plib_course_builder_redirect_success(string $message): void {
    redirect(new moodle_url('/h5p/h5plib/course_builder'),
            $message,
            null,
            \core\output\notification::NOTIFY_SUCCESS);
}

/**
 * Check if a user is enrolled to any course
 *
 * @return bool
 */
function h5plib_course_builder_is_enrolled_to_any_course($user): bool {
    $courses = h5p_course_builder_get_courses();
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
function h5plib_course_builder_no_access_redirect(stdClass $user): void {
    if (!is_siteadmin() && !h5plib_course_builder_is_enrolled_to_any_course($user)) {
        h5plib_course_builder_redirect_error(get_string('nocoursesenrolledin', 'h5plib_course_builder'), '/');
    }

    $courses = h5p_course_builder_get_courses();
    $teacherCourses = h5plib_course_builder_check_if_teacher_in_courses($user, $courses);
    if (!is_siteadmin() && sizeof($teacherCourses) < 1) {
        h5plib_course_builder_redirect_error(get_string('noteditingteacherinanycourse', 'h5plib_course_builder'), '/');
    }
}

/**
 * Allows to delete outdated enrolments from the database
 *
 * @param stdClass $user
 * @return void
 */
function h5plib_course_builder_delete_user_enrolments(stdClass $user): void {
    global $DB;

    $userId = $user->id;

    $sql = "SELECT course.id
        FROM {user_enrolments} userenrol
        JOIN {enrol} enrol ON userenrol.enrolid = enrol.id
        JOIN {course} course ON enrol.courseid = course.id
        WHERE userenrol.userid = :userid
        AND userenrol.timeend < :now and userenrol.timeend != 0";

    $params = ['userid' => $userId, 'now' => time()];
    $courseUtilisation = $DB->get_records_sql($sql, $params);

    foreach ($courseUtilisation as $course) {
        $enrolInstance = $DB->get_record('enrol', ['courseid' => $course->id, 'enrol' => 'manual']);
        $enrolplugin = enrol_get_plugin($enrolInstance->enrol);
        $enrolplugin->unenrol_user($enrolInstance, $userId);
    }
}

/**
 * Displays the plugin's logo on the page
 *
 * @return void
 */
function h5plib_course_builder_display_logo(): void {
    echo html_writer::tag('br', '');
    echo html_writer::tag('br', '');
    echo html_writer::start_tag('center');
    echo html_writer::empty_tag('img', [
            'src' => 'medias/img/course_builder_logo.png',
            'width' => '200px',
            'alt' => 'logo',
    ]);
    echo html_writer::end_tag('center');
}

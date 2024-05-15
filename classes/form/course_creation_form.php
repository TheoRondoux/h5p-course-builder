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

namespace h5plib_poc_editor\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/accesslib.php');
require_once($CFG->libdir . '/formslib.php');
require_once($CFG->dirroot . '/h5p/h5plib/poc_editor/lib.php');

class course_creation_form extends \moodleform {
    public function definition() {
        global $USER;

        $courseCreationForm = $this->_form;

        $courseCreationForm->addElement('text', 'presentation_title', get_string('presentationtitle', 'h5plib_poc_editor'));
        $courseCreationForm->setType('presentation_title', PARAM_TEXT);
        $courseCreationForm->addRule(
                'presentation_title',
                get_string('requieredpresentationtitle', 'h5plib_poc_editor'),
                'required',
                '',
                'client'
        );
        if (!h5plib_poc_editor_is_enrolled_to_any_course($USER)) {
            h5plib_poc_editor_redirect_error(get_string('nocoursesenrolledin', 'h5plib_poc_editor'));
        }

        $courses = h5p_poc_editor_get_courses();
        $teacherCourses = h5plib_poc_editor_check_if_teacher_in_courses($USER, $courses);
        if (sizeof($teacherCourses) < 1) {
            h5plib_poc_editor_redirect_error(get_string('noteditingteacherinanycourse', 'h5plib_poc_editor'));
        }

        $coursesNames = [];
        $coursesNames[] = get_string('selectcoursetext', 'h5plib_poc_editor');
        foreach ($teacherCourses as $course) {
            $coursesNames[] = $course->fullname;
        }

        $courseCreationForm->addElement('select', 'course_select', get_string('coursechoice', 'h5plib_poc_editor'), $coursesNames);
        $courseCreationForm->addRule(
                'course_select',
                get_string('requieredcoursechoice', 'h5plib_poc_editor'),
                'required',
                '',
                'client'
        );

        $rowTemplates = h5p_poc_editor_get_added_templates();
        $templatesNames = h5p_poc_editor_get_templates_names($rowTemplates);

        $courseCreationForm->addElement('select', 'template_select', get_string('selecttemplate', 'h5plib_poc_editor'),
                $templatesNames);
        $courseCreationForm->addRule(
                'template_select',
                get_string('requiredtemplatechoice', 'h5plib_poc_editor'),
                'required',
                '',
                'client'
        );

        $courseCreationForm->addElement('textarea', 'presentation_intro', get_string('presentationintro', 'h5plib_poc_editor'),
                'wrap="virtual" rows="7" cols="20"');
        $courseCreationForm->addElement('advcheckbox', 'share_presentation', get_string('sharepresentation', 'h5plib_poc_editor'),
                ' ', ['shared' => 1], [0, 1]);
        $courseCreationForm->addElement('submit', 'createpresentationsubmitbutton',
                get_string('createpresentation', 'h5plib_poc_editor'));
    }
}

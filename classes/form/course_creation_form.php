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
 */

namespace h5plib_course_builder\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/accesslib.php');
require_once($CFG->libdir . '/formslib.php');
require_once($CFG->dirroot . '/h5p/h5plib/course_builder/lib.php');
require_once($CFG->dirroot . '/h5p/h5plib/course_builder/extra/libs/attribute_lib.php');

class course_creation_form extends \moodleform {
    public function definition() {
        global $USER;

        $courseCreationForm = $this->_form;
        $courseCreationForm->addElement('text', 'presentation_title', get_string('presentationtitle', 'h5plib_course_builder'));
        $courseCreationForm->setType('presentation_title', PARAM_TEXT);
        $courseCreationForm->addRule(
                'presentation_title',
                get_string('requieredpresentationtitle', 'h5plib_course_builder'),
                'required',
                '',
                'client'
        );

        $courses = h5p_course_builder_get_courses();
        $teacherCourses = h5plib_course_builder_check_if_teacher_in_courses($USER, $courses);
        if (sizeof($teacherCourses) < 1) {
            h5plib_course_builder_redirect_error(get_string('noteditingteacherinanycourse', 'h5plib_course_builder'));
        }

        $coursesNames = [];
        $coursesNames[] = get_string('selectcoursetext', 'h5plib_course_builder');
        foreach ($teacherCourses as $course) {
            $coursesNames[] = $course->fullname;
        }

        $courseCreationForm->addElement('select', 'course_select', get_string('coursechoice', 'h5plib_course_builder'),
                $coursesNames);
        $courseCreationForm->addRule(
                'course_select',
                get_string('requieredcoursechoice', 'h5plib_course_builder'),
                'required',
                '',
                'client'
        );

        $rowTemplates = h5p_course_builder_get_added_templates();
        $templatesNames = h5p_course_builder_get_templates_names($rowTemplates);

        $courseCreationForm->addElement('select', 'template_select', get_string('selecttemplate', 'h5plib_course_builder'),
                $templatesNames);
        $courseCreationForm->addRule(
                'template_select',
                get_string('requiredtemplatechoice', 'h5plib_course_builder'),
                'required',
                '',
                'client'
        );

        $courseCreationForm->addElement('textarea', 'presentation_intro', get_string('presentationintro', 'h5plib_course_builder'),
                'wrap="virtual" rows="7" cols="20"');
        $courseCreationForm->addElement('advcheckbox', 'share_presentation',
                get_string('sharepresentation', 'h5plib_course_builder'),
                ' ', ['shared' => 1], [0, 1]);
        $courseCreationForm->addElement('submit', 'createpresentationsubmitbutton',
                get_string('createpresentation', 'h5plib_course_builder'), h5plib_course_builder_get_custom_btn_attributes());
    }
}

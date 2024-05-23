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

require_once($CFG->libdir . '/formslib.php');
require_once($CFG->dirroot . '/config.php');
require_once($CFG->dirroot . '/h5p/h5plib/course_builder/extra/libs/attribute_lib.php');

class config_form extends \moodleform {
    public function definition() {
        global $DB;
        $form = $this->_form;

        $form->addElement('html', '<h3>' . get_string("generalconfiguration", "h5plib_course_builder") . '</h3>');

        $templateCourse = $DB->get_record('course', ['shortname' => 'coursebuilder']);
        if (!$templateCourse) {
            $form->addElement('html', '<center><p> ' . get_string('notemplatecreated', 'h5plib_course_builder') . ' </p></center>');
            $form->addElement('submit', 'create_editor_template_course', get_string('createcourse', 'h5plib_course_builder'),
                    h5plib_course_builder_get_custom_btn_attributes());
        } else {
            $form->addElement('html',
                    '<center><p>Template course already created, you can click on the button below to access it</p></center>');
            $form->addElement('html', '<center><a href="' . new \moodle_url("/course/view.php?id=" . $templateCourse->id) .
                    '">' . get_string('gototemplatesrepo', 'h5plib_course_builder') . '</a></center>',
                    h5plib_course_builder_get_custom_btn_attributes());
        }
    }
}

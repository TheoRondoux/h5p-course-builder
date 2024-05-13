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

require_once($CFG->libdir . '/formslib.php');
require_once($CFG->dirroot. '/h5p/h5plib/poc_editor/lib.php');

class course_creation_form extends \moodleform {
    public function definition() {
        global $DB;

        $ccform = $this->_form;

        $ccform->addElement('text', 'presentation_title', get_string('presentationtitle', 'h5plib_poc_editor'));
        $ccform->setType('presentation_title', PARAM_TEXT);
        $ccform->addRule(
            'presentation_title', 
            get_string('requieredpresentationtitle', 'h5plib_poc_editor'), 
            'required',
            '',
            'client'
        );

        $courses = h5p_poc_editor_get_courses();
        $coursesname = [];
        array_push($coursesname, get_string('selectcoursetext', 'h5plib_poc_editor'));
        foreach ( $courses as $c ) {
            array_push($coursesname, $c->fullname);
        }
        $ccform->addElement('select', 'course_select', get_string('coursechoice', 'h5plib_poc_editor'), $coursesname );
        $ccform->addRule(
            'course_select', 
            get_string('requieredcoursechoice', 'h5plib_poc_editor'), 
            'required',
            '',
            'client'
        );

        $rowtemplates = h5p_poc_editor_get_added_templates();
        $templatesnames = h5p_poc_editor_get_templates_names($rowtemplates);

        $ccform->addElement('select', 'template_select', get_string('selecttemplate', 'h5plib_poc_editor'),$templatesnames);
        $ccform->addRule(
            'template_select',
            get_string('requiredtemplatechoice', 'h5plib_poc_editor'),
            'required',
            '',
            'client'
        );

        $ccform->addElement('textarea', 'presentation_intro', get_string('presentationintro', 'h5plib_poc_editor'),'wrap="virtual" rows="7" cols="20"');
        $ccform->addElement('advcheckbox', 'share_presentation', get_string('sharepresentation', 'h5plib_poc_editor'), ' ', array('shared' => 1), array(0,1));
        $ccform->addElement('submit', 'createpresentationsubmitbutton', get_string('createpresentation', 'h5plib_poc_editor'));
    }
}

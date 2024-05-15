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
require_once($CFG->dirroot . '/config.php');
require_once($CFG->dirroot . '/h5p/h5plib/poc_editor/lib.php');

class config_add_template_form extends \moodleform {
    public function definition() {
        $form = $this->_form;

        $form->addElement('html', '<h4>' . get_string('addtemplate', 'h5plib_poc_editor') . '</h4>');

        $templateCourse = h5p_poc_editor_get_template_course();
        if (!empty($templateCourse->id)) {
            $templateCourseId = $templateCourse->id;
            $addedTemplates = h5p_poc_editor_get_added_templates();
            $availableTemplates = h5p_poc_editor_get_available_templates($addedTemplates, $templateCourseId);
            if (count($availableTemplates) > 0) {
                $availableTemplatesNames = [];
                foreach ($availableTemplates as $availableTemplate) {
                    $availableTemplatesNames[] = $availableTemplate->name;
                }
                $form->addElement('select', 'available_templates', get_string('availabletemplates', 'h5plib_poc_editor'),
                        $availableTemplatesNames);
                $form->addElement('submit', 'submit_add_template', get_string('addselectedtemplate', 'h5plib_poc_editor'));
            } else {
                $form->addElement('html', '<center><p>' . get_string('nonewtemplates', 'h5plib_poc_editor') . '</p></center>');
            }
        }

    }
}
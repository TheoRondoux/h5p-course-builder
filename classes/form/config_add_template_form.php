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
        $mform = $this->_form;

        $mform->addElement('html', '<h3>Add a template</h3>');

        $templatecourseid = h5p_poc_editor_get_template_course()->id;
        $addedtemplates = h5p_poc_editor_get_added_templates(); 
        $availabletemplates = h5p_poc_editor_get_available_templates($addedtemplates, $templatecourseid);
        if (count($availabletemplates) > 0) {
            $availabletemplatesnames = [];
            foreach ($availabletemplates as $availabletemplate) {
                array_push($availabletemplatesnames, $availabletemplate->name);
            }
            $mform->addElement('select', 'available_templates', 'Available templates', $availabletemplatesnames);
            $mform->addElement('submit', 'submit_add_template', 'Add selected template');
        }
        else {
            $mform->addElement('html', '<center><p>No new templates to add. Everything\'s clear!</p></center>');
        }
    }
}
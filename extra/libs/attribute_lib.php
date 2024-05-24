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
 *
 * @package     h5plib_course_builder
 * @copyright   2024 - Godfred Boaheng
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 *
 */

/**
 * Used to get the attributes for the back button
 *
 * @return array $attributes
 * @var moodle_url $back_url
 */

function h5plib_course_builder_get_back_btn_attributes() {
    $back_url = new moodle_url('/h5p/h5plib/course_builder/');
    $attributes = [
            'href' => $back_url,
            'role' => 'button',
            'class' => 'btn',
            'style' =>
                    '
            display: inline-block;
            text-align: center;
            vertical-align: middle;
            user-select: none;
            background-color: #3F2A56; 
            border-radius: 5px; 
            padding: 10px; 
            border-color: #3F2A56;
            color: white;',
    ];
    return $attributes;
}

/**
 * Used to get the attributes for the create button
 *
 * @return array $attributes
 */

function h5plib_course_builder_get_create_btn_attributes() {
    $attributes = [
            'href' => 'creation_form.php',
            'role' => 'button',
            'class' => 'btn',
            'style' =>
                    '
            background-color: #3F2A56;
            border-radius: 5px; 
            border-color: #3F2A56;
            padding:20px;
            color : #fff;',
    ];
    return $attributes;
}

/**
 * Used to get the attributes for the custom button
 *
 * @return array $attributes
 */

function h5plib_course_builder_get_custom_btn_attributes(): array {
    $attributes = [
            'role' => 'button',
            'style' =>
                    '
            background-color: #3F2A56;
            border-radius: 4px; 
            border-color: #3F2A56;
            padding: 10px;
            color : #fff;',
    ];
    return $attributes;
}

/**
 * Used to get the attributes for the presentation button
 *
 * @return array $attributes
 */

function h5plib_course_builder_get_presentation_btn_attributes(): array {
    $attributes = [
            'href' => 'content.php',
            'role' => 'button',
            'class' => 'btn',
            'style' =>
                    '
            background-color: #3F2A56;
            border-radius: 5px; 
            border-color: #3F2A56;
            padding:20px;
            color : #fff;',
    ];
    return $attributes;
}

/**
 * Used to get the attributes for the edit button
 *
 * @param moodle_url $edit_url
 * @return array $attributes
 */

function h5plib_course_builder_get_edit_btn_attributes($edit_url): array {
    $attributes = [
            'href' => $edit_url,
            'role' => 'button',
            'class' => 'btn',
            'style' =>
                    '
            background-color: #3F2A56;
            border-radius: 5px; 
            border-color: #3F2A56;
            padding:10px;
            color : #fff;',
    ];
    return $attributes;
}

/**
 * Used to get the attributes for the left navigation button
 *
 * @return array $attributes
 *
 */
function h5plib_course_builder_get_left_nav_btn_attributes(): array {

    $attributes = [
            'href' => '#carouselExampleIndicators',
            'class' => 'btn mb-3 mr-1',
            'data-slide' => 'prev',
            'role' => 'button',
            'style' =>
                    '
            background-color: #3F2A56;
            border-radius: 4px; 
            border-color: #3F2A56;
            padding: 10px;
            color : #fff;',
    ];
    return $attributes;
}

/**
 * Used to get the attributes for the right navigation button
 *
 * @return array $attributes
 */

function h5plib_course_builder_get_right_nav_btn_attributes(): array {
    $attributes = [
            'href' => '#carouselExampleIndicators',
            'class' => 'btn mb-3',
            'data-slide' => 'next',
            'role' => 'button',
            'style' =>
                    '
            background-color: #3F2A56;
            border-radius: 4px; 
            border-color: #3F2A56;
            padding: 10px;
            color : #fff;',
    ];
    return $attributes;
}

  
<?php
function h5plib_poc_editor_get_back_btn_attributes() {
    $back_url = new moodle_url('/h5p/h5plib/poc_editor/');
    $attributes = array(
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
            color: white;'
        );
    return $attributes;
}

function h5plib_poc_editor_get_create_btn_attributes() {
    $attributes = array(
        'href' => 'creation_form.php',
        'role' => 'button',
        'class' => 'btn',
        'style' => 
            '
            background-color: #3F2A56;
            border-radius: 5px; 
            border-color: #3F2A56;
            padding:20px;
            color : #fff;'
        );
    return $attributes;
}

function h5plib_poc_editor_get_custom_btn_attributes() {
    $attributes = array(
        'role' => 'button',
        'style' => 
            '
            background-color: #3F2A56;
            border-radius: 4px; 
            border-color: #3F2A56;
            padding: 10px;
            color : #fff;'
        );
    return $attributes;
}

function h5plib_poc_editor_get_presentation_btn_attributes() {
    $attributes = array(
        'href' => 'presentations.php',
        'role' => 'button',
        'class' => 'btn',
        'style' => 
            '
            background-color: #3F2A56;
            border-radius: 5px; 
            border-color: #3F2A56;
            padding:20px;
            color : #fff;'
        );
    return $attributes;
}


function h5plib_poc_editor_get_edit_btn_attributes($edit_url) {
    $attributes = array(
        'href' => $edit_url,
        'role' => 'button',
        'class' => 'btn',
        'style' => 
            '
            background-color: #3F2A56;
            border-radius: 5px; 
            border-color: #3F2A56;
            padding:10px;
            color : #fff;'
        );
    return $attributes;
}

function h5plib_poc_editor_get_left_nav_btn_attributes() {
    $carousel_nav_icon_left = '<i class="fa fa-arrow-left"></i>';
    $carousel_nav_icon_right = '<i class="fa fa-arrow-right"></i>';
    $attributes = array(
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
            color : #fff;'
        );
    return $attributes;
}

function h5plib_poc_editor_get_right_nav_btn_attributes() {
    $attributes = array(
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
            color : #fff;'
        );
    return $attributes;
}

  
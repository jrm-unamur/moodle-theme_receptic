<?php
// This file is part of Moodle - http://moodle.org/
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package    
 * @author     Jean-Roch Meurisse
 * @copyright  2016 - Cellule TICE - Universite de Namur
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Function that generates a chunk of SCSS to be prepended to the main scss file.
function theme_receptic_get_pre_scss($theme) {
    // 1. To define our own configurable scss variables use the code below and comment code under 2.
    global $CFG;

    $scss = '';
    $configurable = [
        // Config key => [scss variableName1, ...].
        'brandcolor' => ['brand-primary'],
    ];

    // Prepend variables first.
    foreach ($configurable as $configkey => $targets) {
        $value = isset($theme->settings->{$configkey}) ? $theme->settings->{$configkey} : null;
        if (empty($value)) {
            continue;
        }

        array_map(function($target) use (&$scss, $value) {
            $scss .= '$' . $target . ': ' . $value . ";\n";
        }, (array) $targets);
    }

    // Then prepend pre-scss code added in theme config.
    if (!empty($theme->settings->scsspre)) {
        $scss .= $theme->settings->scsspre;
    }

    return $scss;

    // 2. To use 'brandcolor' and 'scsspre' variables from boost theme. Value is therefore defined in boost theme and not in our custom one. To use this code uncomment below and comment code under 1.
    /*$theme = theme_config::load('boost');
    return theme_boost_get_pre_scss($theme);*/
}

// Function that generates a chunk of SCSS code to be added to the end of the main scss file.
function theme_receptic_get_extra_scss($theme) {
    // 1. To define our own extra scss variable. To use it uncomment the code below and comment under 2.
    return !empty($theme->settings->scss) ? $theme->settings->scss : '';
    // 2. To use 'scss' variable from boost theme. (See theme_receptic_get_pre_scss -> 2). To use it uncomment the code below and uncomment under 1.
    /*$theme = theme_config::load('boost');
    return theme_boost_get_extra_scss($theme);*/
}

function theme_receptic_get_main_scss_content($theme) {
    global $CFG;

    $scss = '';
    $filename = !empty($theme->settings->preset) ? $theme->settings->preset : null;
    $fs = get_file_storage();
    $context = context_system::instance();

    if ($filename == 'default.scss') {
        $scss .= file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/default.scss');
    } else if ($filename == 'plain.scss') {
        $scss .= file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/plain.scss');
    } else if ($filename && ($presetfile = $fs->get_file($context->id, 'theme_receptic', 'preset', 0, '/', $filename))) {
        $scss .= $presetfile->get_content();
    } else {
        $scss .= file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/default.scss');
    }

    // Add 2 scss file to the beginning and end of main
    $pre = file_get_contents($CFG->dirroot . '/theme/receptic/scss/pre.scss');
    $fa = file_get_contents($CFG->dirroot . '/theme/receptic/scss/fontawesome/font-awesome.scss');
    $post = file_get_contents($CFG->dirroot . '/theme/receptic/scss/post.scss');

    return $pre . "\n" . $scss . "\n" . $post;
}

// Function to update an image loaded through the theme settings pages.
function theme_receptic_update_settings_images($settingname) {

    global $CFG;

    // The setting name comes as a string like 's_theme_receptic_settingname'.
    // Split it to get the actual setting name.
    $parts = explode('_', $settingname);
    $settingname = end($parts);

    // Get context. Admin settings are stored in system context.
    $syscontext = context_system::instance();
    $component = 'theme_receptic';

    // Filename of the uploaded file for the setting.
    $filename = get_config($component, $settingname);

    // Store file extension in a variable for further use.
    $extension = substr($filename, strrpos($filename, '.') + 1);

    // Path in the moodle file system
    $fullpath = "/{$syscontext->id}/{$component}/{$settingname}/0{$filename}";

    $fs = get_file_storage();

    // Best way to get a file if we know the exact path.
    if ($file = $fs->get_file_by_hash(sha1($fullpath))) {

        // The stored file has been found --> copy it to dataroot in a location matched by the search for location in theme_config::resolve_image_location.
        $pathname = $CFG->dataroot . '/pix_plugins/theme/receptic/' . $settingname . '.' . $extension;

        // Retrieve any previous files with maybe different path extensions.
        $pathpattern = $CFG->dataroot . '/pix_plugins/theme/receptic/' . $settingname . '.*';

        // Make sure directory exists.
        @mkdir($CFG->dataroot . '/pix_plugins/theme/receptic/', $CFG->directorypermissions, true);

        // Delete any existing files for this setting.
        foreach (glob($pathpattern) as $filename) {
            @unlink($filename);
        }

        // Copy the new file to the specified location ($pathname).
        $file->copy_content_to($pathname);
    }
    theme_reset_all_caches();
}

function theme_receptic_get_fontawesome_icon_map() {
    return [
        //'core:t/delete' => 'fa-futbol-o',
        ];
}


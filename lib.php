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
 * @package    theme_receptic
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
}

// Function that generates a chunk of SCSS code to be added to the end of the main scss file.
function theme_receptic_get_extra_scss($theme) {
    // 1. To define our own extra scss variable. To use it uncomment the code below and comment under 2.
    return !empty($theme->settings->scss) ? $theme->settings->scss : '';
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

    // Add 2 scss file to the beginning and end of main.
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

    // Path in the moodle file system.
    $fullpath = "/{$syscontext->id}/{$component}/{$settingname}/0{$filename}";

    $fs = get_file_storage();

    // Best way to get a file if we know the exact path.
    if ($file = $fs->get_file_by_hash(sha1($fullpath))) {

        // The stored file has been found --> copy it to dataroot in a location
        // matched by the search for location in theme_config::resolve_image_location.
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
        ];
}

/**
 * Serves any files associated with the theme settings.
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param context $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @param array $options
 * @return bool
 */
function theme_receptic_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array()) {

    if ($context->contextlevel == CONTEXT_SYSTEM && ($filearea === 'logoleft' || $filearea === 'logoright' || $filearea === 'logocenter')) {
        $theme = theme_config::load('receptic');
        // By default, theme files must be cache-able by both browsers and proxies.
        if (!array_key_exists('cacheability', $options)) {
            $options['cacheability'] = 'public';
        }
        return $theme->setting_file_serve($filearea, $args, $forcedownload, $options);
    } else {
        send_file_not_found();
    }
}

function theme_receptic_reset_flashbox_teachers() {
    global $DB;
    $DB->execute("UPDATE {user_preferences}
                     SET value = :value
                   WHERE name = :name", array('value' => 'false', 'name' => 'flashbox-teacher-hidden'));
}

function theme_receptic_reset_flashbox_students() {
    global $DB;
    $DB->execute("UPDATE {user_preferences}
                     SET value = :value
                   WHERE name = :name", array('value' => 'false', 'name' => 'flashbox-student-hidden'));
}

/*function theme_receptic_extend_navigation(global_navigation $nav) {
    global $COURSE;
    // Ajouter une condition pour n'afficher que pour les createurs de cours.
    if (has_capability('local/createcourse:create', context_system::instance())) {
        $syscontext = context_system::instance();
        //if ($COURSE->id == SITEID) {
        $noeitem = $nav->add(get_string('createcourse', 'local_createcourse'), '/local/createcourse/index.php',
            navigation_node::TYPE_SETTING, null , 'noecreatecourse2', new pix_icon('i/course' , ''));
        $noeitem->showinflatnavigation = true;
        $noeitem->myshowdivider = true;
        if (has_capability('moodle/course:create', context_system::instance())) {
            $manualitem = $nav->add(get_string('createcourse', 'local_createcourse') . ' ... (manuel2)',
                    '/course/edit.php?category=1&returnto=topcat',
                    navigation_node::TYPE_SETTING, null , 'manualcreatecourse2', new pix_icon('i/course' , ''));
            $manualitem->showinflatnavigation = false;
            if (empty($noeitem)) {
                $manualitem->myshowdivider = true;
            }
        }
    }
}*/
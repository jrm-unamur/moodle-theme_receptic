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
 * @copyright  2016 - Cellule TICE - Unversite de Namur
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    $settings = new theme_boost_admin_settingspage_tabs('themesettingmwar', get_string('configtitle', 'theme_mwar'));
    // General settings page.
    $page = new admin_settingpage('theme_mwar_general', get_string('generalsettings', 'theme_mwar'));

    // Scss variable overriding $brand-primary
    $name = 'theme_mwar/brandcolor';
    $title = get_string('brandcolor_desc', 'theme_mwar');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_mwar/preset';
    $title = get_string('preset', 'theme_mwar');
    $description = get_string('preset_desc', 'theme_mwar');
    $default = 'default.scss';

    // List preset files in our theme file area to add them to the dropdown choice list. We then add presets from boost.
    $context = context_system::instance();
    $fs = get_file_storage();
    $files = $fs->get_area_files($context->id, 'theme_mwar', 'preset', 0, 'itemid, filepath', 'filename', false);
    $choices = [];
    foreach ($files as $file) {
        $choices[$file->get_filename()] = $file->get_filename();
    }
    $choices['default.scss'] = 'default.scss';
    $choices['plain.scss'] = 'plain.scss';

    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Add file uploader to add preset files to our theme
    $name = 'theme_mwar/presetfiles';
    $title = get_string('presetfiles', 'theme_mwar');
    $descriptin = get_string('presetfiles_desc', 'theme_mwar');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'preset', 0,
            array('maxfiles' => 20, 'accepted_types' => array('.scss')));
    $page->add($setting);

    // Add background logo to login page
    $name = 'theme_mwar/loginbackgroundimage';
    $title = get_string('loginbackgroundimage', 'theme_mwar');
    $description = get_string('loginbackgroundimage_desc', 'theme_mwar');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'loginbackgroundimage');
    $setting->set_updatedcallback('theme_mwar_update_settings_images');
    $page->add($setting);
    $settings->add($page);

    // Advanced settings page.
    $page = new admin_settingpage('theme_mwar_advanced', get_string('advancedsettings', 'theme_mwar'));

    // Raw scss code to include before main content.
    $name = 'theme_mwar/scsspre';
    $title = get_string('rawscsspre', 'theme_mwar');
    $description = get_string('rawscsspre_desc', 'theme_mwar');
    $setting = new admin_setting_configtextarea($name, $title, $description, '', PARAM_RAW);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Raw scss code to include after main content.
    $name = 'theme_mwar/scss';
    $title = get_string('rawscss', 'theme_mwar');
    $description = get_string('rawscss_desc', 'theme_mwar');
    $setting = new admin_setting_configtextarea($name, $title, $description, '', PARAM_RAW);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    $settings->add($page);

    $page = new admin_settingpage('theme_mwar_toolselector', get_string('toolselector', 'theme_mwar'));

    if (!$modules = $DB->get_records('modules', array('visible' => 1), 'name ASC')) {
        //print_error('moduledoesnotexist', 'error');
        $modules = array();
    }

    $activities = array();
    $resources = array();

    foreach ($modules as $module) {
        $archetype = plugin_supports('mod', $module->name, FEATURE_MOD_ARCHETYPE, MOD_ARCHETYPE_OTHER);
        $module->localname = get_string('pluginname', 'mod_' . $module->name);
        if ($archetype !== MOD_ARCHETYPE_RESOURCE && $archetype !== MOD_ARCHETYPE_SYSTEM) {
            $activities[] = $module;
        } else if ($archetype == MOD_ARCHETYPE_RESOURCE) {
            $resources[] = $module;
        }
    }

    usort($activities, function($a, $b) {
        return strcmp($a->localname, $b->localname);
    });
    usort($resources, function($a, $b) {
        return strcmp($a->localname, $b->localname);
    });

    //Activities.
    $page->add(new admin_setting_heading('activitieslist', get_string('activitieslist', 'theme_mwar'), get_string('activitieslistdesc', 'theme_mwar')));
    foreach ($activities as $activity) {
        $name = 'theme_mwar/' . $activity->name . 'inshortlist';
        $title = $activity->localname;
        $default = false;
        $setting = new admin_setting_configcheckbox($name, $title, '', $default);
        //$setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);
    }
    // Resources.
    $page->add(new admin_setting_heading('resourceslist', get_string('resourceslist', 'theme_mwar'), get_string('activitieslistdesc', 'theme_mwar')));
    foreach ($resources as $resource) {
        $name = 'theme_mwar/' . $resource->name . 'inshortlist';
        $title = $resource->localname;
        //$description = get_string('enablecategorycolorsdesc', 'theme_mwar');
        $default = false;
        $setting = new admin_setting_configcheckbox($name, $title, '', $default);
        //$setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);
    }
$settings->add($page);
    //$ADMIN->add('theme_mwar', $settingspage);
}
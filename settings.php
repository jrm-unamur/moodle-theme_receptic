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
    $settings = new theme_boost_admin_settingspage_tabs('themesettingreceptic', get_string('configtitle', 'theme_receptic'));
    // General settings page.
    $page = new admin_settingpage('theme_receptic_general', get_string('generalsettings', 'theme_receptic'));

    // Scss variable overriding $brand-primary
    $name = 'theme_receptic/brandcolor';
    $title = get_string('brandcolor_desc', 'theme_receptic');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_receptic/preset';
    $title = get_string('preset', 'theme_receptic');
    $description = get_string('preset_desc', 'theme_receptic');
    $default = 'default.scss';

    // List preset files in our theme file area to add them to the dropdown choice list. We then add presets from boost.
    $context = context_system::instance();
    $fs = get_file_storage();
    $files = $fs->get_area_files($context->id, 'theme_receptic', 'preset', 0, 'itemid, filepath', 'filename', false);
    $choices = [];
    foreach ($files as $file) {
        if ($file->get_filename() !== '.') {
            $choices[$file->get_filename()] = $file->get_filename();
        }
    }
    $choices['default.scss'] = 'default.scss';
    $choices['plain.scss'] = 'plain.scss';

    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Add file uploader to add preset files to our theme
    $name = 'theme_receptic/presetfiles';
    $title = get_string('presetfiles', 'theme_receptic');
    $descriptin = get_string('presetfiles_desc', 'theme_receptic');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'preset', 0,
            array('maxfiles' => 20, 'accepted_types' => array('.scss')));
    $page->add($setting);

    // Add background logo to login page
    $name = 'theme_receptic/loginbackgroundimage';
    $title = get_string('loginbackgroundimage', 'theme_receptic');
    $description = get_string('loginbackgroundimage_desc', 'theme_receptic');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'loginbackgroundimage');
    $setting->set_updatedcallback('theme_receptic_update_settings_images');
    $page->add($setting);
    $settings->add($page);

    // Advanced settings page.
    $page = new admin_settingpage('theme_receptic_advanced', get_string('advancedsettings', 'theme_receptic'));

    // Raw scss code to include before main content.
    $name = 'theme_receptic/scsspre';
    $title = get_string('rawscsspre', 'theme_receptic');
    $description = get_string('rawscsspre_desc', 'theme_receptic');
    $setting = new admin_setting_scsscode($name, $title, $description, '', PARAM_RAW);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Raw scss code to include after main content.
    $name = 'theme_receptic/scss';
    $title = get_string('rawscss', 'theme_receptic');
    $description = get_string('rawscss_desc', 'theme_receptic');
    $setting = new admin_setting_scsscode($name, $title, $description, '', PARAM_RAW);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    $settings->add($page);

    $page = new admin_settingpage('theme_receptic_toolselector', get_string('toolselector', 'theme_receptic'));

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
    $page->add(new admin_setting_heading('activitieslist', get_string('activitieslist', 'theme_receptic'), get_string('activitieslistdesc', 'theme_receptic')));
    foreach ($activities as $activity) {
        $name = 'theme_receptic/' . $activity->name . 'inshortlist';
        $title = $activity->localname;
        $default = false;
        $setting = new admin_setting_configcheckbox($name, $title, '', $default);
        $page->add($setting);
    }
    // Resources.
    $page->add(new admin_setting_heading('resourceslist', get_string('resourceslist', 'theme_receptic'), get_string('activitieslistdesc', 'theme_receptic')));
    foreach ($resources as $resource) {
        $name = 'theme_receptic/' . $resource->name . 'inshortlist';
        $title = $resource->localname;
        //$description = get_string('enablecategorycolorsdesc', 'theme_receptic');
        $default = false;
        $setting = new admin_setting_configcheckbox($name, $title, '', $default);
        $page->add($setting);
    }
    $settings->add($page);

    $page = new admin_settingpage('theme_receptic_toolbarsettings', get_string('toolbarsettings', 'theme_receptic'));
    // Enable/disable navigation drawer.
    $name = 'theme_receptic/enablenavdrawer';
    $title = get_string('enablenavdrawer', 'theme_receptic');
    $description = get_string('enablenavdrawer_desc', 'theme_receptic');
    $setting = new admin_setting_configcheckbox($name, $title, $description, 0);
    $page->add($setting);

    // Add a home link to the navigation bar.
    $name = 'theme_receptic/navbarhomelink';
    $title = get_string('navbarhomelink', 'theme_receptic');
    $description = get_string('navbarhomelink_desc', 'theme_receptic');
    $setting = new admin_setting_configcheckbox($name, $title, $description, 0);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Add a dashboard link to the navigation bar.
    $name = 'theme_receptic/navbardashboardlink';
    $title = get_string('navbardashboardlink', 'theme_receptic');
    $description = get_string('navbardashboardlink_desc', 'theme_receptic');
    $setting = new admin_setting_configcheckbox($name, $title, $description, 0);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Add a calendar link to the navigation bar.
    $name = 'theme_receptic/navbarcalendarlink';
    $title = get_string('navbarcalendarlink', 'theme_receptic');
    $description = get_string('navbarcalendarlink_desc', 'theme_receptic');
    $setting = new admin_setting_configcheckbox($name, $title, $description, 0);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Add a private files link to the navigation bar.
    $name = 'theme_receptic/privatefileslink';
    $title = get_string('privatefileslink', 'theme_receptic');
    $description = get_string('privatefileslink_desc', 'theme_receptic');
    $setting = new admin_setting_configcheckbox($name, $title, $description, 0);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Add user course list to the navigation bar.
    $name = 'theme_receptic/personalcourselistintoolbar';
    $title = get_string('personalcourselistintoolbar', 'theme_receptic');
    $description = get_string('personalcourselistintoolbar_desc', 'theme_receptic');
    $setting = new admin_setting_configcheckbox($name, $title, $description, 0);
    $page->add($setting);

    $settings->add($page);

}
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
 * @copyright  2016 - Cellule TICE - Unversite de Namur
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    $settings = new theme_boost_admin_settingspage_tabs('themesettingreceptic', get_string('configtitle', 'theme_receptic'));
    // General settings page.
    $page = new admin_settingpage('theme_receptic_general', get_string('generalsettings', 'theme_receptic'));

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

    // Add file uploader to add preset files to our theme.
    $name = 'theme_receptic/presetfiles';
    $title = get_string('presetfiles', 'theme_receptic');
    $descriptin = get_string('presetfiles_desc', 'theme_receptic');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'preset', 0,
            array('maxfiles' => 20, 'accepted_types' => array('.scss')));
    $page->add($setting);

    // Add background logo to login page.
    $name = 'theme_receptic/loginbackgroundimage';
    $title = get_string('loginbackgroundimage', 'theme_receptic');
    $description = get_string('loginbackgroundimage_desc', 'theme_receptic');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'loginbackgroundimage');
    $setting->set_updatedcallback('theme_receptic_update_settings_images');
    $page->add($setting);
    $settings->add($page);

    $page = new admin_settingpage('theme_receptic_colours', get_string('branding', 'theme_receptic'));

    // Scss variable to override $brand-primary.
    $name = 'theme_receptic/brandprimary';
    $title = get_string('brandcolor', 'theme_receptic');
    $desription = get_string('brandcolor_desc', 'theme_receptic');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Variable $brandprimary.
    $name = 'theme_receptic/brandbanner';
    $title = get_string('brandbanner', 'theme_receptic');
    $description = get_string('brandbanner_desc', 'theme_receptic');
    $default = true;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Scss variable to override $brand-banner-color.
    $name = 'theme_receptic/brandbannercolor';
    $title = get_string('brandbannercolor', 'theme_receptic');
    $description = get_string('brandbannercolor_desc', 'theme_receptic');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Left logo file setting.
    $title = get_string('logoleft', 'theme_receptic');
    $description = get_string('logoleft_desc', 'theme_receptic');
    $setting = new admin_setting_configstoredfile('theme_receptic/logoleft', $title, $description, 'logoleft', 0,
        ['maxfiles' => 1, 'accepted_types' => ['.jpg', '.png']]);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Center logo file setting.
    $title = get_string('logocenter', 'theme_receptic');
    $description = get_string('logocenter_desc', 'theme_receptic');
    $setting = new admin_setting_configstoredfile('theme_receptic/logocenter', $title, $description, 'logocenter', 0,
        ['maxfiles' => 1, 'accepted_types' => ['.jpg', '.png']]);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Right logo file setting.
    $title = get_string('logoright', 'theme_receptic');
    $description = get_string('logoright_desc', 'theme_receptic');
    $setting = new admin_setting_configstoredfile('theme_receptic/logoright', $title, $description, 'logoright', 0,
        ['maxfiles' => 1, 'accepted_types' => ['.jpg', '.png']]);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $settings->add($page);

    // Dashboard settings page.
    $page = new admin_settingpage('theme_receptic_dashboard', get_string('myhome'));

    $page->add(new admin_setting_heading('dashboardcoursefiltering',
        get_string('dashboardcoursefiltering', 'theme_receptic'), ''));

    // Activate mixed view dashboard.
    $name = 'theme_receptic/allowcoursefiltering';
    $title = get_string('allowcoursefiltering', 'theme_receptic');
    $description = get_string('allowcoursefiltering_desc', 'theme_receptic');
    $default = true;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
    $page->add($setting);

    $name = 'theme_receptic/coursefilterall';
    $title = get_string('all', 'block_myoverview');
    $description = get_string('coursefilterall_desc', 'theme_receptic');
    $default = true;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
    $page->add($setting);

    $name = 'theme_receptic/coursefilterinprogress';
    $title = get_string('inprogress', 'block_myoverview');
    $description = get_string('coursefilterinprogress_desc', 'theme_receptic');
    $default = true;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
    $page->add($setting);

    $name = 'theme_receptic/coursefilterfuture';
    $title = get_string('future', 'block_myoverview');
    $description = get_string('coursefilterfuture_desc', 'theme_receptic');
    $default = true;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
    $page->add($setting);

    $name = 'theme_receptic/coursefilterpast';
    $title = get_string('past', 'block_myoverview');
    $description = get_string('coursefilterall_desc', 'theme_receptic');
    $default = true;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
    $page->add($setting);

    $name = 'theme_receptic/coursefilterstarred';
    $title = get_string('favourites', 'block_myoverview');
    $description = get_string('coursefilterstarred_desc', 'theme_receptic');
    $default = true;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
    $page->add($setting);

    $name = 'theme_receptic/coursefilterhidden';
    $title = get_string('hidden', 'block_myoverview');
    $description = get_string('coursefilterhidden_desc', 'theme_receptic');
    $default = true;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
    $page->add($setting);

    $page->add(new admin_setting_heading('dashboardcoursesorting',
        get_string('dashboardcoursesorting', 'theme_receptic'), ''));

    $name = 'theme_receptic/allowcoursesorting';
    $title = get_string('allowcoursesorting', 'theme_receptic');
    $description = get_string('allowcoursesorting_desc', 'theme_receptic');
    $default = true;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
    $page->add($setting);

    $page->add(new admin_setting_heading('dashboarddisplaymode',
        get_string('dashboarddisplaymode', 'theme_receptic'), ''));

    $name = 'theme_receptic/allowdisplaymode';
    $title = get_string('allowdisplaymode', 'theme_receptic');
    $description = get_string('allowdisplaymode_desc', 'theme_receptic');
    $default = true;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
    $page->add($setting);

    $page->add(new admin_setting_heading('createcourse',
        get_string('createcourse', 'theme_receptic'), ''));

    // Add a button to create a course in course creators' dashboard.
    $name = 'theme_receptic/addcoursebutton';
    $title = get_string('addcoursebutton', 'theme_receptic');
    $description = get_string('addcoursebutton_desc', 'theme_receptic');
    $default = true;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
    $page->add($setting);

    // Url to a local specific method to create a course.
    $name = 'theme_receptic/localcreatecourseplugin';
    $title = get_string('localcreatecourseplugin', 'theme_receptic');
    $description = get_string('localcreatecourseplugin_desc', 'theme_receptic');
    $setting = new admin_setting_configtext($name, $title, $description, '');
    $page->add($setting);

    $page->add(new admin_setting_heading('bulkenrol',
        get_string('bulkenrol', 'theme_receptic'), ''));

    // Add a button to allow students to enrol to their official list of courses.
    // Requires a local plugin able to give a list of courses to which the current student must be enrolled to.
    $name = 'theme_receptic/bulkenrolme';
    $title = get_string('bulkenrolme', 'theme_receptic');
    $description = get_string('bulkenrolme_desc', 'theme_receptic');
    $setting = new admin_setting_configcheckbox($name, $title, $description, false);
    $page->add($setting);

    // Name of bulk enrolme plugin.
    $name = 'theme_receptic/bulkenrolmeplugin';
    $title = get_string('bulkenrolmeplugin', 'theme_receptic');
    $description = get_string('bulkenrolmeplugin_desc', 'theme_receptic');
    $setting = new admin_setting_configtext($name, $title, $description, '');
    $page->add($setting);

    // Name of bulk enrolme plugin.
    $name = 'theme_receptic/bulkenrolmefile';
    $title = get_string('bulkenrolmefile', 'theme_receptic');
    $description = get_string('bulkenrolmefile_desc', 'theme_receptic');
    $default = 'bulkenrolme.php';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $page->add($setting);

    // Email pattern for bulk enrolme button.
    $name = 'theme_receptic/bulkenrolemailpattern';
    $title = get_string('bulkenrolemailpattern', 'theme_receptic');
    $description = get_string('bulkenrolemailpattern_desc', 'theme_receptic');
    $setting = new admin_setting_configtext($name, $title, $description, '');
    $page->add($setting);

    $page->add(new admin_setting_heading('otherdashboardelements',
        get_string('otherdashboardelements', 'theme_receptic'), ''));

    $name = 'theme_receptic/togglecoursevisibility';
    $title = get_string('togglecoursevisibility', 'theme_receptic');
    $description = get_string('togglecoursevisibility_desc', 'theme_receptic');
    $setting = new admin_setting_configcheckbox($name, $title, $description, false);
    $page->add($setting);

    $name = 'theme_receptic/unenrolme';
    $title = get_string('unenrolme', 'theme_receptic');
    $description = get_string('unenrolme_desc', 'theme_receptic');
    $setting = new admin_setting_configcheckbox($name, $title, $description, false);
    $page->add($setting);

    $name = 'theme_receptic/showprogress';
    $title = get_string('showprogress', 'theme_receptic');
    $description = get_string('showprogress_desc', 'theme_receptic');
    $setting = new admin_setting_configcheckbox($name, $title, $description, false);
    $page->add($setting);

    $settings->add($page);

    $page = new admin_settingpage('theme_receptic_toolselector', get_string('toolselector', 'theme_receptic'));

    if (!$modules = $DB->get_records('modules', array('visible' => 1), 'name ASC')) {
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

    // Activities.
    $page->add(new admin_setting_heading('activitieslist',
            get_string('activitieslist', 'theme_receptic'),
            get_string('activitieslistdesc', 'theme_receptic')));
    foreach ($activities as $activity) {
        $name = 'theme_receptic/' . $activity->name . 'inshortlist';
        $title = $activity->localname;
        $default = false;
        $setting = new admin_setting_configcheckbox($name, $title, '', $default);
        $page->add($setting);
    }
    // Resources.
    $page->add(new admin_setting_heading('resourceslist',
            get_string('resourceslist', 'theme_receptic'),
            get_string('activitieslistdesc', 'theme_receptic')));
    foreach ($resources as $resource) {
        $name = 'theme_receptic/' . $resource->name . 'inshortlist';
        $title = $resource->localname;
        $default = false;
        $setting = new admin_setting_configcheckbox($name, $title, '', $default);
        $page->add($setting);
    }
    $settings->add($page);

    $page = new admin_settingpage('theme_receptic_toolbarsettings', get_string('toolbarsettings', 'theme_receptic'));

    // Add an edit button in the navigation bar.
    $name = 'theme_receptic/editbutton';
    $title = get_string('editbutton', 'theme_receptic');
    $description = get_string('editbuttondesc', 'theme_receptic');
    $default = 0;
    $choices = array(0 => 'No', 1 => 'Yes');
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $page->add($setting);

    // Hide default edit button.
    $name = 'theme_receptic/hidedefaulteditingbutton';
    $title = get_string('hidedefaulteditingbutton', 'theme_receptic');
    $description = get_string('hidedefaulteditingbutton_desc', 'theme_receptic');
    $default = false;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
    $page->add($setting);

    $settings->add($page);

    // Configure redballs and orangeballs.
    $page = new admin_settingpage('theme_receptic_redballs', get_string('redballs', 'theme_receptic'));
    $name = 'theme_receptic/enableballs';
    $title = get_string('enableballs', 'theme_receptic');
    $description = get_string('enableballs_desc', 'theme_receptic');
    $setting = new admin_setting_configcheckbox($name, $title, $description, true);
    $page->add($setting);

    $options = array(
        30  => new lang_string('nummonth', '', 1),
        21  => new lang_string('numweeks', '', 3),
        14  => new lang_string('numweeks', '', 2),
        7  => new lang_string('numweek', '', 1),
        6  => new lang_string('numdays', '', 6),
        5  => new lang_string('numdays', '', 5),
        4  => new lang_string('numdays', '', 4),
        3  => new lang_string('numdays', '', 3),
        2  => new lang_string('numdays', '', 2),
        1  => new lang_string('numday', '', 1));
    $name = 'theme_receptic/hotitemslookback';
    $title = get_string('hotitemslookback', 'theme_receptic');
    $description = get_string('hotitemslookback_desc', 'theme_receptic');
    $setting = new admin_setting_configselect($name, $title, $description, 30, $options);
    $page->add($setting);

    $settings->add($page);

    // Configure information in page footer.
    $page = new admin_settingpage('theme_receptic_footer', get_string('footer', 'theme_receptic'));

    // Contact information.
    $name = 'theme_receptic/contactheader';
    $title = get_string('contactheader', 'theme_receptic');
    $description = get_string('contactheader_desc', 'theme_receptic');
    $setting = new admin_setting_configtext($name, $title, $description, '');
    $page->add($setting);
    $name = 'theme_receptic/contactemail';
    $title = get_string('contactemail', 'theme_receptic');
    $description = get_string('contactemail_desc', 'theme_receptic');
    $default = $CFG->supportemail;
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $page->add($setting);

    $name = 'theme_receptic/contactphone';
    $title = get_string('contactphone', 'theme_receptic');
    $description = get_string('contactphone_desc', 'theme_receptic');
    $setting = new admin_setting_configtext($name, $title, $description, '');
    $page->add($setting);

    // Moodle credits.
    $name = 'theme_receptic/moodlecredits';
    $title = get_string('moodlecredits', 'theme_receptic');
    $description = get_string('moodlecredits_desc', 'theme_receptic');
    $setting = new admin_setting_configcheckbox($name, $title, $description, true);
    $page->add($setting);

    // Login info.
    $name = 'theme_receptic/logininfo';
    $title = get_string('logininfo', 'theme_receptic');
    $description = get_string('logininfo_desc', 'theme_receptic');
    $setting = new admin_setting_configcheckbox($name, $title, $description, false);
    $page->add($setting);

    $settings->add($page);
}

$page = new admin_settingpage('theme_receptic_flashbox', get_string('flashboxes', 'theme_receptic'), 'theme/receptic:editflashbox');

// Flashboxteachers setting.
$name = 'theme_receptic/flashboxteachers';
$title = get_string('flashboxteachers', 'theme_receptic');
$description = get_string('flashboxteachers_desc', 'theme_receptic');
$default = '';
$setting = new admin_setting_confightmleditor($name, $title, $description, $default);
$setting->set_updatedcallback('theme_receptic_reset_flashbox_teachers');
$page->add($setting);

// Flashboxteacherstype setting.
$name = 'theme_receptic/flashboxteacherstype';
$title = get_string('flashboxteacherstype', 'theme_receptic');
$description = get_string('flashboxteacherstype_desc', 'theme_receptic');
$default = 'warning';
$choices = [
    'warning' => get_string('warning'),
    'trick' => get_string('trick', 'theme_receptic'),
    'info' => get_string('info')
];

$setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
$page->add($setting);

// Flashboxstudents setting.
$name = 'theme_receptic/flashboxstudents';
$title = get_string('flashboxstudents', 'theme_receptic');
$description = get_string('flashboxstudents_desc', 'theme_receptic');
$default = '';
$setting = new admin_setting_confightmleditor($name, $title, $description, $default);
$setting->set_updatedcallback('theme_receptic_reset_flashbox_students');
$page->add($setting);

// Flashboxstudentstype setting.
$name = 'theme_receptic/flashboxstudentstype';
$title = get_string('flashboxstudentstype', 'theme_receptic');
$description = get_string('flashboxstudentstype_desc', 'theme_receptic');
$default = 'warning';
$choices = [
    'warning' => get_string('warning'),
    'trick' => get_string('trick', 'theme_receptic'),
    'info' => get_string('info')
];

$setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
$page->add($setting);

// Add settings page to the appearance settings category.
$ADMIN->add('appearance', $page);

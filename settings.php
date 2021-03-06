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
 * Receptic theme settings file.
 *
 * @package    theme_receptic
 * @author     Jean-Roch Meurisse
 * @copyright  2016 - Cellule TICE - Unversite de Namur
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/cohort/lib.php');
require_once($CFG->dirroot.'/theme/receptic/lib.php');

if ($ADMIN->fulltree) {
    $settings = new theme_boost_admin_settingspage_tabs('themesettingreceptic', get_string('configtitle', 'theme_receptic'));
    // General settings page.
    $page = new admin_settingpage('theme_receptic_general', get_string('generalsettings', 'theme_receptic'));

    // Preset.
    $page->add(new admin_setting_heading('presetheading',
        get_string('presetheading', 'theme_receptic'), ''));

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

    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Add file uploader to add preset files to our theme.
    $name = 'theme_receptic/presetfiles';
    $title = get_string('presetfiles', 'theme_receptic');
    $description = get_string('presetfiles_desc', 'theme_receptic');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'preset', 0,
            array('maxfiles' => 20, 'accepted_types' => array('.scss')));
    $page->add($setting);

    // Scss variable to override $brand-primary.
    $name = 'theme_receptic/brandprimary';
    $title = get_string('brandcolor', 'theme_receptic');
    $description = get_string('brandcolor_desc', 'theme_receptic');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Brand banner.
    $page->add(new admin_setting_heading('brandbannerheading',
        get_string('brandbannerheading', 'theme_receptic'), ''));
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

    // Logos for brand banner.
    global $SITE;
    $defaulttarget = $CFG->wwwroot . '?redirect=0';
    $defaultalt = format_string($SITE->shortname, true, ['context' => context_course::instance(SITEID), "escape" => false]);

    // Left logo file setting.
    $title = get_string('logoleft', 'theme_receptic');
    $description = get_string('logoleft_desc', 'theme_receptic');
    $setting = new admin_setting_configstoredfile('theme_receptic/logoleft', $title, $description, 'logoleft', 0,
        ['maxfiles' => 1, 'accepted_types' => ['.jpg', '.png']]);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Target url for left logo.
    $name = 'theme_receptic/logolefturl';
    $title = get_string('logolefturl', 'theme_receptic');
    $setting = new admin_setting_configtext($name, $title, '', $defaulttarget);
    $page->add($setting);

    // Alt text for left logo.
    $name = 'theme_receptic/logoleftalt';
    $title = get_string('logoleftalt', 'theme_receptic');
    $setting = new admin_setting_configtext($name, $title, '', $defaultalt);
    $page->add($setting);

    // Center logo file setting.
    $title = get_string('logocenter', 'theme_receptic');
    $description = get_string('logocenter_desc', 'theme_receptic');
    $setting = new admin_setting_configstoredfile('theme_receptic/logocenter', $title, $description, 'logocenter', 0,
        ['maxfiles' => 1, 'accepted_types' => ['.jpg', '.png']]);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Target url for center logo.
    $name = 'theme_receptic/logocenterurl';
    $title = '';
    $setting = new admin_setting_configtext($name, $title, '', $defaulttarget);
    $page->add($setting);

    // Alt text for center logo.
    $name = 'theme_receptic/logocenteralt';
    $title = get_string('logocenteralt', 'theme_receptic');
    $setting = new admin_setting_configtext($name, $title, '', $defaultalt);
    $page->add($setting);

    // Right logo file setting.
    $title = get_string('logoright', 'theme_receptic');
    $description = get_string('logoright_desc', 'theme_receptic');
    $setting = new admin_setting_configstoredfile('theme_receptic/logoright', $title, $description, 'logoright', 0,
        ['maxfiles' => 1, 'accepted_types' => ['.jpg', '.png']]);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Target url for right logo.
    $name = 'theme_receptic/logorighturl';
    $title = get_string('logorighturl', 'theme_receptic');
    $setting = new admin_setting_configtext($name, $title, '', $defaulttarget);
    $page->add($setting);

    // Alt text for right logo.
    $name = 'theme_receptic/logorightalt';
    $title = get_string('logorightalt', 'theme_receptic');
    $setting = new admin_setting_configtext($name, $title, '', $defaultalt);
    $page->add($setting);

    // Favicon.
    $page->add(new admin_setting_heading('faviconheading',
        get_string('faviconheading', 'theme_receptic'), ''));

    // Favicon upload.
    $name = 'theme_receptic/favicon';
    $title = get_string ('favicon', 'theme_receptic' );
    $description = get_string ('favicon_desc', 'theme_receptic' );
    $setting = new admin_setting_configstoredfile( $name, $title, $description, 'favicon', 0,
        array('maxfiles' => 1, 'accepted_types' => array('.png', '.jpg', '.ico')));
    $setting->set_updatedcallback ( 'theme_reset_all_caches' );
    $page->add($setting);

    // Custom SCSS.
    $page->add(new admin_setting_heading('customscss',
        get_string('customscss', 'theme_receptic'), ''));

    // Raw SCSS to include before the content.
    $setting = new admin_setting_scsscode('theme_receptic/scsspre',
        get_string('rawscsspre', 'theme_receptic'), get_string('rawscsspre_desc', 'theme_receptic'), '', PARAM_RAW);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Raw SCSS to include after the content.
    $setting = new admin_setting_scsscode('theme_receptic/scss', get_string('rawscss', 'theme_receptic'),
        get_string('rawscss_desc', 'theme_receptic'), '', PARAM_RAW);
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
    $description = get_string('coursefilterpast_desc', 'theme_receptic');
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

    $name = 'theme_receptic/forceddisplaymode';
    $title = get_string('forceddisplaymode', 'theme_receptic');
    $description = get_string('forceddisplaymode_desc', 'theme_receptic');
    $default = 'list';
    $choices = [
        'list' => get_string('list', 'block_myoverview'),
        'card' => get_string('card', 'block_myoverview'),
        'summary' => get_string('summary', 'block_myoverview')
    ];
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
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
    $setting->set_updatedcallback('theme_receptic_disable_user_hidden_courses');
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
            get_string('activitieslist_desc', 'theme_receptic')));
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
            get_string('resourceslist_desc', 'theme_receptic')));
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
    $description = get_string('editbutton_desc', 'theme_receptic');
    $default = 0;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
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

    // Configure footer elements.
    $page = new admin_settingpage('theme_receptic_course', get_string('course'));

    // Setting to display the course settings page as a panel within the course.
    $name = 'theme_receptic/settingsincoursepage';
    $title = get_string('settingsincoursepage', 'theme_receptic', null, true);
    $description = get_string('settingsincoursepage_desc', 'theme_receptic', null, true);
    $setting = new admin_setting_configcheckbox($name, $title, $description, 'no', 'yes', 'no'); // Overriding default values
    // yes = 1 and no = 0 because of the use of empty() in theme_boost_campus_get_pre_scss() (lib.php).
    // Default 0 value would not write the variable to scss that could cause the scss to crash if used in that file.
    // See MDL-58376.
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    // Contact information.
    $name = 'theme_receptic/activitynavigation';
    $title = get_string('activitynavigation', 'theme_receptic');
    $description = get_string('activitynavigation_desc', 'theme_receptic');
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
    $page->add($setting);

    // Contact information.
    $name = 'theme_receptic/hiddencoursewarning';
    $title = get_string('showhiddencoursewarning', 'theme_receptic');
    $description = get_string('showhiddencoursewarning_desc', 'theme_receptic');
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
    $page->add($setting);

    // Contact information.
    $name = 'theme_receptic/switchedrolewarning';
    $title = get_string('showswitchedrolewarning', 'theme_receptic');
    $description = get_string('showswitchedrolewarning_desc', 'theme_receptic');
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
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
    $default = !empty($CFG->supportemail) ? $CFG->supportemail : '';
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

    // Add miscellaneous settings.
    $page = new admin_settingpage('theme_receptic_misc', get_string('miscellaneous'));

    // Add setting to prevent users from uploading profile picture.
    $name = 'theme_receptic/disableavatarupload';
    $title = get_string('disableavatarupload', 'theme_receptic');
    $description = get_string('disableavatarupload_desc', 'theme_receptic');
    $setting = new admin_setting_configcheckbox($name, $title, $description, false);
    $page->add($setting);

    // Add textarea to insert email patters of users for which profile picture upload must be deactivated.
    $name = 'theme_receptic/disableavataruploademailpattern';
    $title = get_string('disableavataruploademailpattern', 'theme_receptic');
    $description = get_string('disableavataruploademailpattern_desc', 'theme_receptic');
    $setting = new admin_setting_configtextarea($name, $title, $description, '', PARAM_RAW);
    $page->add($setting);

    $name = 'theme_receptic/shrinkablenavdrawer';
    $title = get_string('shrinkablenavdrawer', 'theme_receptic', null, true);
    $description = get_string('shrinkablenavdrawer_desc', 'theme_receptic', null, true);
    $setting = new admin_setting_configcheckbox($name, $title, $description, false);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_receptic/helptextinmodal';
    $title = get_string('helptextinmodal', 'theme_receptic', null, true);
    $description = get_string('helptextinmodal_desc', 'theme_receptic', null, true);
    $setting = new admin_setting_configcheckbox($name, $title, $description, false);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_receptic/extrajsmodule';
    $title = get_string('extrajsmodule', 'theme_receptic', null, true);
    $description = get_string('extrajsmodule_desc', 'theme_receptic', null, true);
    $setting = new admin_setting_configtext($name, $title, $description, '');
    $page->add($setting);

    $name = 'theme_receptic/extrajsmoduleargs';
    $title = get_string('extrajsmoduleargs', 'theme_receptic', null, true);
    $description = get_string('extrajsmoduleargs_desc', 'theme_receptic', null, true);
    $setting = new admin_setting_configtextarea($name, $title, $description, '', PARAM_RAW);
    $page->add($setting);

    // Add tab to settings page.
    $settings->add($page);
}
$page = new admin_settingpage('theme_receptic_flashbox', get_string('flashboxes', 'theme_receptic'), 'theme/receptic:editflashbox');

// Flashbox1 setting.
$name = 'theme_receptic/flashbox1';
$title = get_string('flashbox1', 'theme_receptic');
$description = get_string('flashbox1_desc', 'theme_receptic');
$default = '';
$setting = new admin_setting_confightmleditor($name, $title, $description, $default);
$setting->set_updatedcallback('theme_receptic_reset_flashbox1');
$page->add($setting);

// Flashbox1type setting.
$name = 'theme_receptic/flashbox1type';
$title = get_string('flashboxtype', 'theme_receptic');
$description = get_string('flashboxtype_desc', 'theme_receptic');
$default = 'warning';
$choices = [
    'warning' => get_string('warning'),
    'trick' => get_string('trick', 'theme_receptic'),
    'info' => get_string('info')
];

$setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
$page->add($setting);

// Flashbox1 for all setting.
$name = 'theme_receptic/flashbox1forall';
$title = get_string('flashboxforall', 'theme_receptic');
$description = get_string('flashboxforall_desc', 'theme_receptic');

$setting = new admin_setting_configcheckbox($name, $title, $description, false);
$page->add($setting);

$name = 'theme_receptic/flashbox1cohorts';
$title = get_string('flashboxcohorts', 'theme_receptic');
$description = get_string('flashboxcohorts_desc', 'theme_receptic');

$cohorts = cohort_get_all_cohorts(0, 0);

$choices = array('' => '-- aucune --');
foreach ($cohorts['cohorts'] as $cohort) {
    $choices[$cohort->id] = $cohort->name;
}

$setting = new admin_setting_configmultiselect($name, $title, $description, array(), $choices);
$page->add($setting);

// Flashbox1 dismissable setting.
$name = 'theme_receptic/flashbox1dismissable';
$title = get_string('flashboxdismissable', 'theme_receptic');
$description = get_string('flashboxdismissable_desc', 'theme_receptic');

$setting = new admin_setting_configcheckbox($name, $title, $description, true);
$page->add($setting);

// Flashbox2 setting.
$name = 'theme_receptic/flashbox2';
$title = get_string('flashbox2', 'theme_receptic');
$description = get_string('flashbox2_desc', 'theme_receptic');
$default = '';
$setting = new admin_setting_confightmleditor($name, $title, $description, $default);
$setting->set_updatedcallback('theme_receptic_reset_flashbox2');
$page->add($setting);

$name = 'theme_receptic/flashbox2cohorts';
$title = get_string('flashboxcohorts', 'theme_receptic');
$description = get_string('flashboxcohorts_desc', 'theme_receptic');
$cohorts = cohort_get_all_cohorts(0, 0);

$choices = array('' => '-- aucune --');
foreach ($cohorts['cohorts'] as $cohort) {
    $choices[$cohort->id] = $cohort->name;
}
$setting = new admin_setting_configmultiselect($name, $title, $description, array(), $choices);
$page->add($setting);

// Flashbox2 type setting.
$name = 'theme_receptic/flashbox2type';
$title = get_string('flashboxtype', 'theme_receptic');
$description = get_string('flashboxtype_desc', 'theme_receptic');
$default = 'warning';
$choices = [
    'warning' => get_string('warning'),
    'trick' => get_string('trick', 'theme_receptic'),
    'info' => get_string('info')
];

$setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
$page->add($setting);

// Flashbox2 dismissable setting.
$name = 'theme_receptic/flashbox2dismissable';
$title = get_string('flashboxdismissable', 'theme_receptic');
$description = get_string('flashboxdismissable_desc', 'theme_receptic');

$setting = new admin_setting_configcheckbox($name, $title, $description, true);
$page->add($setting);

// Add settings page to the appearance settings category.
$ADMIN->add('appearance', $page);

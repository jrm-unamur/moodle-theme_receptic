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
    global $CFG, $DB;

    $scss = '';

    $configurable = [
        // Config key => [scss variableName1, ...].
        'brandbannercolor' => ['brand-banner-color'],
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
        $tmp = new stdClass();
        $tmp->trace = $scss;
        $DB->insert_record('webcampus_trace', $tmp);
    }
    if ($theme->settings->brandbanner) {
        $scss .= '$brand-banner-height:80px;';
    } else {
        $scss .= '$brand-banner-height: 0px;';
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
    if ($filename == 'boostlike.scss') {
        $scss .= file_get_contents($CFG->dirroot . '/theme/receptic/scss/preset/boostlike.scss');
    } else if ($filename == 'unamur35.scss') {
        $scss .= file_get_contents($CFG->dirroot . '/theme/receptic/scss/preset/unamur35.scss');
    } else if ($filename && ($presetfile = $fs->get_file($context->id, 'theme_receptic', 'preset', 0, '/', $filename))) {
        $scss .= $presetfile->get_content();
    }
    // Add 2 scss file to the beginning and end of main.
    $pre = file_get_contents($CFG->dirroot . '/theme/receptic/scss/pre.scss');
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

    if ($context->contextlevel == CONTEXT_SYSTEM
            && ($filearea === 'logoleft' || $filearea === 'logoright' || $filearea === 'logocenter')) {
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

function theme_receptic_set_brandbanner_height() {
    global $DB;
    if (get_config('theme_receptic', 'brandbanner')) {
        set_config('brandbannerheight', '80px', 'theme_receptic');
    } else {
        set_config('brandbannerheight', '0px', 'theme_receptic');
    }
}

function theme_receptic_get_redballs($course, $starttime) {
    global $DB, $USER;

    $count = 0;
    $query = "SELECT id, contextinstanceid, timecreated
                    FROM {logstore_standard_log}
                   WHERE contextlevel = :contextlevel
                     AND courseid = :courseid
                     AND userid != :userid
                     AND eventname = '\\\core\\\\event\\\course_module_created'
                     AND timecreated > :starttime
                     AND contextinstanceid IN (SELECT id
                                                 FROM {course_modules})
                ORDER BY timecreated DESC";

    $records = $DB->get_records_sql($query, array(
        'contextlevel' => CONTEXT_MODULE,
        'courseid' => $course->id,
        'userid' => $USER->id,
        'starttime' => $starttime,
        'update' => 'u',
        'create' => 'c'
    ));

    $alreadytested = array();
    $redcmids = array();
    foreach ($records as $record) {
        if (in_array($record->contextinstanceid, $alreadytested)) {
            continue;
        } else {
            $alreadytested[] = $record->contextinstanceid;
        }
        $modlabelid = $DB->get_field('modules', 'id', array('name' => 'label'));
        if ($DB->record_exists('course_modules', array('module' => $modlabelid, 'id' => $record->contextinstanceid))) {
            $query = "SELECT *
                            FROM {logstore_standard_log}
                           WHERE contextlevel = :contextlevel
                             AND eventname = :event
                             AND courseid = :courseid
                             AND timecreated > :timestamp
                             AND userid = :userid
                             AND crud = :crud";

            $conditions = array(
                'contextlevel' => CONTEXT_COURSE,
                'event' => '\core\event\course_viewed',
                'courseid' => $course->id,
                'crud' => 'r',
                'userid' => $USER->id,
                'timestamp' => $record->timecreated
            );
        } else {
            $query = "SELECT *
                            FROM {logstore_standard_log}
                           WHERE contextlevel = :contextlevel
                             AND courseid = :courseid
                             AND timecreated > :timestamp
                             AND contextinstanceid = :contextinstanceid
                             AND userid = :userid
                             AND crud = :crud";

            $conditions = array(
                'contextlevel' => CONTEXT_MODULE,
                'courseid' => $course->id,
                'crud' => 'r',
                'userid' => $USER->id,
                'contextinstanceid' => $record->contextinstanceid,
                'timestamp' => $record->timecreated
            );
        }

        if (!$DB->get_records_sql($query, $conditions)) {

            $count++;

            $redcmids[] = $record->contextinstanceid;
        }
    }
    return $redcmids;
}

function theme_receptic_get_orangeballs($course, $starttime) {
    global $DB, $USER;
    $count = 0;
    $modlabelid = $DB->get_field('modules', 'id', array('name' => 'label'));
    $query = "SELECT id, eventname, objectid, contextinstanceid, timecreated
                    FROM {logstore_standard_log}
                   WHERE contextlevel = :contextlevel
                     AND courseid = :courseid
                     AND userid != :userid
                     AND timecreated > :starttime
                     AND (
                            (
                                (eventname = '\\\mod_folder\\\\event\\\\folder_updated'
                                OR eventname = '\\\mod_wiki\\\\event\\\page_updated'
                                OR eventname = '\\\mod_book\\\\event\\\chapter_created'
                                OR eventname = '\\\mod_book\\\\event\\\chapter_updated'
                                OR eventname = '\\\mod_glossary\\\\event\\\\entry_created'
                                OR eventname = '\\\mod_glossary\\\\event\\\\entry_updated'
                                OR eventname = '\\\mod_data\\\\event\\\\record_created'
                                OR eventname = '\\\mod_data\\\\event\\\\record_updated'
                                )

                                AND contextinstanceid IN (SELECT id
                                                            FROM {course_modules})
                            )
                         OR (eventname = '\\\core\\\\event\\\course_module_updated'
                               AND contextinstanceid IN (SELECT id FROM {course_modules} where module = :modlabelid)
                            )
                         )
                ORDER BY timecreated DESC";

    $records = $DB->get_records_sql($query, array(
        'contextlevel' => CONTEXT_MODULE,
        'courseid' => $course->id,
        'userid' => $USER->id,
        'starttime' => $starttime,
        'update' => 'u',
        'create' => 'c',
        'modlabelid' => $modlabelid
    ));

    $alreadytested = array();
    $redcmids = array();
    foreach ($records as $record) {
        if (in_array($record->contextinstanceid, $alreadytested)) {
            continue;
        } else {
            $alreadytested[] = $record->contextinstanceid;
        }
        $modglossaryid = $DB->get_field('modules', 'id', array('name' => 'glossary'));
        if ($record->eventname == '\mod_glossary\event\entry_updated'
                || $record->eventname == '\mod_glossary\event\entry_created') {
            $glossaryentry = $DB->get_record('glossary_entries', array('id' => $record->objectid));
            if ($glossaryentry->approved == 0) {
                continue;
            }
        }
        $modlabelid = $DB->get_field('modules', 'id', array('name' => 'label'));
        if ($DB->record_exists('course_modules', array('module' => $modlabelid, 'id' => $record->contextinstanceid))) {
            $query = "SELECT *
                            FROM {logstore_standard_log}
                           WHERE contextlevel = :contextlevel
                             AND eventname = :event
                             AND courseid = :courseid
                             AND timecreated > :timestamp
                             AND userid = :userid
                             AND crud = :crud";

            $conditions = array(
                'contextlevel' => CONTEXT_COURSE,
                'event' => '\core\event\course_viewed',
                'courseid' => $course->id,
                'crud' => 'r',
                'userid' => $USER->id,
                'timestamp' => $record->timecreated
            );
        } else {
            $query = "SELECT *
                            FROM {logstore_standard_log}
                           WHERE contextlevel = :contextlevel
                             AND courseid = :courseid
                             AND timecreated > :timestamp
                             AND contextinstanceid = :contextinstanceid
                             AND userid = :userid
                             AND crud = :crud";

            $conditions = array(
                'contextlevel' => CONTEXT_MODULE,
                'courseid' => $course->id,
                'crud' => 'r',
                'userid' => $USER->id,
                'contextinstanceid' => $record->contextinstanceid,
                'timestamp' => $record->timecreated
            );
        }

        if (!$DB->get_records_sql($query, $conditions)) {

            $count++;

            $redcmids[] = $record->contextinstanceid;
        }
    }
    return $redcmids;
}

function theme_receptic_init_vars_for_hot_items_computing() {
    global $DB, $USER;
    $redballs = get_user_preferences('user_redballs');
    $newitems = array();
    if (!empty($redballs)) {
        $newitems = explode(',' , $redballs);
    }
    $orangeballs = get_user_preferences('user_orangeballs');
    $updateditems = array();
    if (!empty($orangeballs)) {
        $updateditems = explode(',', $orangeballs);
    }
    if (is_null($redballs) && is_null($orangeballs)) {
        $defaultballslookback = get_config('theme_receptic', 'hotitemslookback');
        $starttime = time() - ($defaultballslookback * 24 * 60 * 60);
    } else {
        $starttime = $DB->get_field('user', 'lastlogin', array('id' => $USER->id));
    }
    return array($newitems, $updateditems, $starttime);
}

function theme_receptic_compute_redballs($course, $starttime, $newitemsforuser = array()) {
    global $DB, $USER;

    $count = 0;
    $query = "SELECT id, contextinstanceid, timecreated
                    FROM {logstore_standard_log}
                   WHERE contextlevel = :contextlevel
                     AND courseid = :courseid
                     AND userid != :userid
                     AND eventname = '\\\core\\\\event\\\course_module_created'
                     AND timecreated > :starttime
                     AND contextinstanceid IN (SELECT id
                                                 FROM {course_modules})
                ORDER BY timecreated DESC";

    $records = $DB->get_records_sql($query, array(
        'contextlevel' => CONTEXT_MODULE,
        'courseid' => $course->id,
        'userid' => $USER->id,
        'starttime' => $starttime,
        'update' => 'u',
        'create' => 'c'
    ));

    $alreadytested = array();
    $redcmids = array();
    foreach ($records as $record) {
        if (in_array($record->contextinstanceid, $alreadytested)) {
            continue;
        } else {
            $alreadytested[] = $record->contextinstanceid;
        }
        $modlabelid = $DB->get_field('modules', 'id', array('name' => 'label'));
        if ($DB->record_exists('course_modules', array('module' => $modlabelid, 'id' => $record->contextinstanceid))) {
            $query = "SELECT *
                            FROM {logstore_standard_log}
                           WHERE contextlevel = :contextlevel
                             AND eventname = :event
                             AND courseid = :courseid
                             AND timecreated > :timestamp
                             AND userid = :userid
                             AND crud = :crud";

            $conditions = array(
                'contextlevel' => CONTEXT_COURSE,
                'event' => '\core\event\course_viewed',
                'courseid' => $course->id,
                'crud' => 'r',
                'userid' => $USER->id,
                'timestamp' => $record->timecreated
            );
        } else {
            $query = "SELECT *
                            FROM {logstore_standard_log}
                           WHERE contextlevel = :contextlevel
                             AND courseid = :courseid
                             AND timecreated > :timestamp
                             AND contextinstanceid = :contextinstanceid
                             AND userid = :userid
                             AND crud = :crud";

            $conditions = array(
                'contextlevel' => CONTEXT_MODULE,
                'courseid' => $course->id,
                'crud' => 'r',
                'userid' => $USER->id,
                'contextinstanceid' => $record->contextinstanceid,
                'timestamp' => $record->timecreated
            );
        }

        if (!$DB->get_records_sql($query, $conditions)) {

            $count++;

            $redcmids[] = $record->contextinstanceid;
        }
    }
    $newitemsforuser = array_merge($newitemsforuser, $redcmids);
    $newitemsforuser = array_unique($newitemsforuser);
    rsort($newitemsforuser);
    if (!empty($newitemsforuser)) {
        $chunks = array_chunk($newitemsforuser, 100);
        set_user_preference('user_redballs', implode(',', $chunks[0]));
        return $chunks[0];
    }
    return $newitemsforuser;
}

function theme_receptic_compute_orangeballs($course, $starttime, $updateditemsforuser = array()) {
    global $DB, $USER;
    $count = 0;
    $modlabelid = $DB->get_field('modules', 'id', array('name' => 'label'));
    $query = "SELECT id, eventname, objectid, contextinstanceid, timecreated
                    FROM {logstore_standard_log}
                   WHERE contextlevel = :contextlevel
                     AND courseid = :courseid
                     AND userid != :userid
                     AND timecreated > :starttime
                     AND (
                            (
                                (eventname = '\\\mod_folder\\\\event\\\\folder_updated'
                                OR eventname = '\\\mod_wiki\\\\event\\\page_updated'
                                OR eventname = '\\\mod_book\\\\event\\\chapter_created'
                                OR eventname = '\\\mod_book\\\\event\\\chapter_updated'
                                OR eventname = '\\\mod_glossary\\\\event\\\\entry_created'
                                OR eventname = '\\\mod_glossary\\\\event\\\\entry_updated'
                                OR eventname = '\\\mod_data\\\\event\\\\record_created'
                                OR eventname = '\\\mod_data\\\\event\\\\record_updated'
                                )

                                AND contextinstanceid IN (SELECT id
                                                            FROM {course_modules})
                            )
                         OR (eventname = '\\\core\\\\event\\\course_module_updated'
                               AND contextinstanceid IN (SELECT id FROM {course_modules} where module = :modlabelid)
                            )
                         )
                ORDER BY timecreated DESC";

    $records = $DB->get_records_sql($query, array(
        'contextlevel' => CONTEXT_MODULE,
        'courseid' => $course->id,
        'userid' => $USER->id,
        'starttime' => $starttime,
        'update' => 'u',
        'create' => 'c',
        'modlabelid' => $modlabelid
    ));

    $alreadytested = array();
    $orangecmids = array();
    foreach ($records as $record) {
        if (in_array($record->contextinstanceid, $alreadytested)) {
            continue;
        } else {
            $alreadytested[] = $record->contextinstanceid;
        }
        $modglossaryid = $DB->get_field('modules', 'id', array('name' => 'glossary'));
        if ($record->eventname == '\mod_glossary\event\entry_updated'
                || $record->eventname == '\mod_glossary\event\entry_created') {
            $glossaryentry = $DB->get_record('glossary_entries', array('id' => $record->objectid));
            if ($glossaryentry->approved == 0) {
                continue;
            }
        }
        $modlabelid = $DB->get_field('modules', 'id', array('name' => 'label'));
        if ($DB->record_exists('course_modules', array('module' => $modlabelid, 'id' => $record->contextinstanceid))) {
            $query = "SELECT *
                            FROM {logstore_standard_log}
                           WHERE contextlevel = :contextlevel
                             AND eventname = :event
                             AND courseid = :courseid
                             AND timecreated > :timestamp
                             AND userid = :userid
                             AND crud = :crud";

            $conditions = array(
                'contextlevel' => CONTEXT_COURSE,
                'event' => '\core\event\course_viewed',
                'courseid' => $course->id,
                'crud' => 'r',
                'userid' => $USER->id,
                'timestamp' => $record->timecreated
            );
        } else {
            $query = "SELECT *
                            FROM {logstore_standard_log}
                           WHERE contextlevel = :contextlevel
                             AND courseid = :courseid
                             AND timecreated > :timestamp
                             AND contextinstanceid = :contextinstanceid
                             AND userid = :userid
                             AND crud = :crud";

            $conditions = array(
                'contextlevel' => CONTEXT_MODULE,
                'courseid' => $course->id,
                'crud' => 'r',
                'userid' => $USER->id,
                'contextinstanceid' => $record->contextinstanceid,
                'timestamp' => $record->timecreated
            );
        }

        if (!$DB->get_records_sql($query, $conditions)) {

            $count++;

            $orangecmids[] = $record->contextinstanceid;
        }
    }
    $updateditemsforuser = array_merge($updateditemsforuser, $orangecmids);
    $updateditemsforuser = array_unique($updateditemsforuser);

    rsort($updateditemsforuser);
    if (!empty($updateditemsforuser)) {
        $chunks = array_chunk($updateditemsforuser, 100);
        set_user_preference('user_orangeballs', implode(',', $chunks[0]));
        return $chunks[0];
    }
    return $updateditemsforuser;
}

function theme_receptic_get_visible_balls_count($course, $redballs, $orangeballs) {
    global $DB;
    $visiblereditems = array();
    $visibleorangeitems = array();
    $modinfo = get_fast_modinfo($course);

    foreach ($modinfo->cms as $cm) {
        if ($cm->uservisible && !$cm->is_stealth() && in_array($cm->id, $redballs)) {
            $visiblereditems[] = $cm->id;
        }
        if ($cm->uservisible && !$cm->is_stealth() && in_array($cm->id,
                $orangeballs) && !in_array($cm->id, $redballs)
        ) {
            $visibleorangeitems[] = $cm->id;
        }
    }

    $orangecount = 0;
    if (!empty($visibleorangeitems)) {
        $orangecount = $DB->count_records_sql(
            "SELECT COUNT(*)
                     FROM {course_modules}
                    WHERE course = :course
                      AND id IN (" . implode(',', $visibleorangeitems) . ")",
            array('course' => $course->id)
        );
    }

    $redcount = 0;
    if (!empty($visiblereditems)) {
        $redcount = $DB->count_records_sql(
            "SELECT COUNT(*)
                     FROM {course_modules}
                    WHERE course = :course
                      AND id IN (" . implode(',', $visiblereditems) . ")",
            array('course' => $course->id)
        );
    }
    return array($redcount, $orangecount);
}

/**
 * Provides the node for the in-course course or activity settings.
 *
 * @return navigation_node.
 */
function theme_receptic_get_incourse_settings() {
    global $COURSE, $PAGE;
    // Initialize the node with false to prevent problems on pages that do not have a courseadmin node.
    $node = false;
    // If setting showsettingsincourse is enabled.
    if (get_config('theme_receptic', 'settingsincoursepage') == 'yes') {
        // Only search for the courseadmin node if we are within a course or a module context.
        if ($PAGE->context->contextlevel == CONTEXT_COURSE || $PAGE->context->contextlevel == CONTEXT_MODULE) {
            // Get the courseadmin node for the current page.
            $node = $PAGE->settingsnav->find('courseadmin', navigation_node::TYPE_COURSE);
            // Check if $node is not empty for other pages like for example the langauge customization page.
            if (!empty($node)) {
                // If the setting 'incoursesettingsswitchtorole' is enabled add these to the $node.
                if (get_config('theme_boost_campus', 'incoursesettingsswitchtorole') == 'yes' && !is_role_switched($COURSE->id)) {
                    // Build switch role link
                    // We could only access the existing menu item by creating the user menu and traversing it.
                    // So we decided to create this node from scratch with the values copied from Moodle core.
                    $roles = get_switchable_roles($PAGE->context);
                    if (is_array($roles) && (count($roles) > 0)) {
                        // Define the properties for a new tab.
                        $properties = array('text' => get_string('switchroleto', 'theme_boost_campus'),
                            'type' => navigation_node::TYPE_CONTAINER,
                            'key'  => 'switchroletotab');
                        // Create the node.
                        $switchroletabnode = new navigation_node($properties);
                        // Add the tab to the course administration node.
                        $node->add_node($switchroletabnode);
                        // Add the available roles as children nodes to the tab content.
                        foreach ($roles as $key => $role) {
                            $properties = array('action' => new moodle_url('/course/switchrole.php',
                                array('id'         => $COURSE->id,
                                    'switchrole' => $key,
                                    'returnurl'  => $PAGE->url->out_as_local_url(false),
                                    'sesskey'    => sesskey())),
                                'type'   => navigation_node::TYPE_CUSTOM,
                                'text'   => $role);
                            $switchroletabnode->add_node(new navigation_node($properties));
                        }
                    }
                }
            }
        }
        return $node;
    }
}

/**
 * Provides the node for the in-course settings for other contexts.
 *
 * @return navigation_node.
 */
function theme_receptic_get_incourse_activity_settings() {
    global $PAGE;
    $context = $PAGE->context;
    $node = false;
    // If setting showsettingsincourse is enabled.
    if (get_config('theme_boost_campus', 'showsettingsincourse') == 'yes') {
        // Settings belonging to activity or resources.
        if ($context->contextlevel == CONTEXT_MODULE) {
            $node = $PAGE->settingsnav->find('modulesettings', navigation_node::TYPE_SETTING);
        } else if ($context->contextlevel == CONTEXT_COURSECAT) {
            // For course category context, show category settings menu, if we're on the course category page.
            if ($PAGE->pagetype === 'course-index-category') {
                $node = $PAGE->settingsnav->find('categorysettings', navigation_node::TYPE_CONTAINER);
            }
        } else {
            $node = false;
        }
    }
    return $node;
}
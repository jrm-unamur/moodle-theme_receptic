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
 * Receptic theme functions.
 *
 * @package    theme_receptic
 * @author     Jean-Roch Meurisse
 * @copyright  2016 - Cellule TICE - Universite de Namur
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Get SCSS to prepend.
 *
 * @param theme_config $theme The theme config object.
 * @return array
 */
function theme_receptic_get_pre_scss($theme) {

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

/**
 * Inject additional SCSS.
 *
 * @param theme_config $theme The theme config object.
 * @return string
 */
function theme_receptic_get_extra_scss($theme) {
    return !empty($theme->settings->scss) ? $theme->settings->scss : '';
}

/**
 * Returns the main SCSS content.
 *
 * @param theme_config $theme The theme config object.
 * @return string
 */
function theme_receptic_get_main_scss_content($theme) {
    global $CFG;

    $scss = '';
    $filename = !empty($theme->settings->preset) ? $theme->settings->preset : null;
    $fs = get_file_storage();
    $context = context_system::instance();
    if ($filename == 'default.scss') {
        $scss .= file_get_contents($CFG->dirroot . '/theme/receptic/scss/preset/default.scss');
    } else if ($filename && ($presetfile = $fs->get_file($context->id, 'theme_receptic', 'preset', 0, '/', $filename))) {
        $scss .= $presetfile->get_content();
    } else {
        // Safety fallback - maybe new installs etc.
        $scss .= file_get_contents($CFG->dirroot . '/theme/receptic/scss/preset/default.scss');
    }
    // Add 1 scss file to the end of main (mainly for quick test and fix purposes.
    $post = file_get_contents($CFG->dirroot . '/theme/receptic/scss/post.scss');

    return $scss . "\n" . $post;
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
            && ($filearea === 'logoleft' || $filearea === 'logoright' || $filearea === 'logocenter' || $filearea === 'favicon')) {
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

/**
 * Reset flashbox1 status for all users.
 *
 */
function theme_receptic_reset_flashbox1() {
    theme_receptic_resetflashbox('flashbox1');
}

/**
 * Reset flashbox2 status for all users.
 *
 */
function theme_receptic_reset_flashbox2() {
    theme_receptic_resetflashbox('flashbox2');
}

/**
 * Reset a flashbox status for all users.
 * @param string $flashbox The flashbox to reset status for
 */
function theme_receptic_resetflashbox($flashbox) {
    global $DB;
    $DB->execute("UPDATE {user_preferences}
                     SET value = :value
                   WHERE name = :name", array('value' => 'false', 'name' => $flashbox . '-hidden'));
}

/**
 * Init balls arrays from user preferences and timestamp to compute new balls.
 *
 * @return array
 */
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

/**
 * Compute redballs for current user
 *
 * @param stdClass $course The current course
 * @param int $starttime Timestamp to compute red balls from
 * @param array $newitemsforuser Red balls already recorded in user preferences
 * @return array
 */
function theme_receptic_compute_redballs($course, $starttime, $newitemsforuser = array()) {
    global $DB, $USER;

    $query = "SELECT id, contextinstanceid, timecreated, courseid, eventname
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

    $redcmids = theme_receptic_compute_balls($records);
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

/**
 * Compute orangeballs for current user
 *
 * @param stdClass $course The current course
 * @param int $starttime Timestamp to compute orange balls from
 * @param array $updateditemsforuser Orange balls already recorded in user preferences
 * @return array
 */
function theme_receptic_compute_orangeballs($course, $starttime, $updateditemsforuser = array()) {
    global $DB, $USER;
    return array();
    $modlabelid = $DB->get_field('modules', 'id', array('name' => 'label'));
    $query = "SELECT id, eventname, objectid, contextinstanceid, courseid, timecreated
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

    $orangecmids = theme_receptic_compute_balls($records);
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

/**
 * Compute red or orange balls from a list of event records.
 *
 * @param array $candidates Events to be filtered to compute balls
 * @return array
 */
function theme_receptic_compute_balls($candidates) {
    global $DB, $USER;

    $alreadytested = array();
    $hotcmids = array();
    foreach ($candidates as $candidate) {
        if (in_array($candidate->contextinstanceid, $alreadytested)) {
            continue;
        } else {
            $alreadytested[] = $candidate->contextinstanceid;
        }

        if ($candidate->eventname == '\mod_glossary\event\entry_updated'
            || $candidate->eventname == '\mod_glossary\event\entry_created') {
            $glossaryentry = $DB->get_record('glossary_entries', array('id' => $candidate->objectid));
            if ($glossaryentry->approved == 0) {
                continue;
            }
        }
        $modlabelid = $DB->get_field('modules', 'id', array('name' => 'label'));
        if ($DB->record_exists('course_modules', array('module' => $modlabelid, 'id' => $candidate->contextinstanceid))) {
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
                'courseid' => $candidate->courseid,
                'crud' => 'r',
                'userid' => $USER->id,
                'timestamp' => $candidate->timecreated
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
                'courseid' => $candidate->courseid,
                'crud' => 'r',
                'userid' => $USER->id,
                'contextinstanceid' => $candidate->contextinstanceid,
                'timestamp' => $candidate->timecreated
            );
        }

        if (!$DB->get_records_sql($query, $conditions)) {
            $hotcmids[] = $candidate->contextinstanceid;
        }
    }
    return $hotcmids;

}

/**
 * Filter red and orange balls to consider only visible activities/resources for the current user.
 *
 * @param stdClass $course The current course object
 * @param array $redballs The list of candidate red balls
 * @param array $orangeballs The list of candidate orange balls
 * @return array
 */
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
 * Cloned from theme boost_campus.
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
                if (!is_role_switched($COURSE->id)) {
                    // Build switch role link
                    // We could only access the existing menu item by creating the user menu and traversing it.
                    // So we decided to create this node from scratch with the values copied from Moodle core.
                    $roles = get_switchable_roles($PAGE->context);
                    if (is_array($roles) && (count($roles) > 0)) {
                        // Define the properties for a new tab.
                        $properties = array('text' => get_string('switchroleto'),
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
 * Cloned from theme boost_campus.
 *
 * @return navigation_node.
 */
function theme_receptic_get_incourse_activity_settings() {
    global $PAGE;
    $context = $PAGE->context;
    $node = false;
    // If setting showsettingsincourse is enabled.
    if (get_config('theme_receptic', 'settingsincoursepage') == 'yes') {
        // Settings belonging to activity or resources.
        if ($context->contextlevel == CONTEXT_MODULE) {
            $node = $PAGE->settingsnav->find('modulesettings', navigation_node::TYPE_SETTING);
        } else if ($context->contextlevel == CONTEXT_COURSECAT) {
            // For course category context, show category settings menu, if we're on the course category page.
            if ($PAGE->pagetype === 'course-index-category' || $PAGE->pagetype === 'course-management') {
                $node = $PAGE->settingsnav->find('categorysettings', navigation_node::TYPE_CONTAINER);
            }
        } else {
            $node = false;
        }
    }
    return $node;
}

/**
 * Checks if config allows current user to upload their profile picture.
 *
 * @return navigation_node.
 */
function theme_receptic_user_can_upload_profile_picture() {
    global $USER;

    if (empty(get_config('theme_receptic', 'disableavatarupload'))) {
        // Restriction is disabled.
        return true;
    } else if (empty(get_config('theme_receptic', 'disableavataruploademailpattern'))) {
        // Restriction is enabled for all users.
        return false;
    } else {
        // Restriction is enabled for users with an email address matching a defined pattern.
        // Make a new array on delimiter "new line".
        $patterns = explode("\n", get_config('theme_receptic', 'disableavataruploademailpattern'));
        // Check for each pattern.
        foreach ($patterns as $pattern) {

            // Trim setting lines.
            $pattern = trim($pattern);
            // Skip empty lines.
            if (strlen($pattern) == 0) {
                continue;
            }
            if (substr_count($USER->email, $pattern)) {
                return false;
            }
        }
    }

    return true;
}

/**
 * Disables hidden course filters if teachers are allowed to toggle course visibility from the dashboard.
 * This to avoid confusion.
 * @return navigation_node.
 */
function theme_receptic_disable_user_hidden_courses() {
    if (get_config('theme_receptic', 'togglecoursevisibility')) {
        set_config('coursefilterhidden', false, 'theme_receptic');
    }

}
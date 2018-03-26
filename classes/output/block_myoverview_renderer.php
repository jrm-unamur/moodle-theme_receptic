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

require_once($CFG->dirroot . '/blocks/myoverview/classes/output/renderer.php');

/**
 * myoverview block renderer
 *
 * @package    block_myoverview
 * @copyright  2016 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class theme_receptic_block_myoverview_renderer extends \block_myoverview\output\renderer {

    /**
     * Return the main content for the block overview.
     *
     * @param main $main The main renderable
     * @return string HTML string
     */
    public function render_main(\block_myoverview\output\main $main) {
        global $CFG, $USER, $DB;

        if (empty(get_config('theme_receptic', 'mixedviewindashboard'))) {
            return parent::render_main($main);
        } else {

        $data = $main->export_for_template($this);
        $createcourseplugin = core_plugin_manager::instance()->get_plugin_info('local_createcourse');
        if ($createcourseplugin && has_capability('local/createcourse:create', context_system::instance())) {
            $data['urls']['addcourse'] = new moodle_url('/local/createcourse/index.php');
            $data['cancreatecourse'] = true;
        }
        if (substr_count($USER->email, '@student.unamur.be')) {
            $data['urls']['enrolme'] = new moodle_url('/local/unamur/noe/enrolnoecourses.php');
            $data['noestudent'] = true;
        }
        $plugins   = enrol_get_plugins(true);

        $redballsactivated = get_config('theme_receptic', 'enableredballs');
        $orangeballsactivated = get_config('theme_receptic', 'enableorangeballs');

        if ($redballsactivated) {
            $userredballs = get_user_preferences('user_redballs');
            if (is_null($userredballs)) {
                $defaultredballslookback = get_config('theme_receptic', 'redballs_lookback');
                $starttime = time() - ($defaultredballslookback * 24 * 60 * 60);
            } else {
                $starttime = $DB->get_field('user', 'lastlogin', array('id' => $USER->id));
            }

            $newitemsforuser = array();
            if (!empty($userredballs)) {
                $newitemsforuser = explode(',', $userredballs);
            }
        }
        if ($orangeballsactivated) {
            $userorangeballs = get_user_preferences('user_orangeballs');
            if (is_null($userorangeballs)) {
                $defaultlookback = get_config('theme_receptic', 'redballs_lookback');
                $starttime = time() - ($defaultlookback * 24 * 60 * 60);
            } else {
                $starttime = $DB->get_field('user', 'lastlogin', array('id' => $USER->id));
            }

            $updateditemsforuser = array();
            if (!empty($userorangeballs)) {
                $updateditemsforuser = explode(',', $userorangeballs);
            }
        }

            //print_object($userorangeballs);die();
        foreach ($data['coursesview']['inprogress']['pages'] as $page) {
            $courses = $page['courses'];
            foreach ($courses as &$course) {
                $coursecontext = context_course::instance($course->id);
                if (has_capability('moodle/course:update', $coursecontext)) {
                    $course->allowtogglevisibility = true;
                    $course->togglevisibilityurl = $CFG->wwwroot . '/theme/receptic/utils.php';
                    $course->sesskey = sesskey();
                }
                if ($redballsactivated) {
                    $visibleorangeitems = array();
                    $updateditemsforcourse = $this->get_orangeballs($course, $starttime);
                    //print_object($updateditemsforcourse);
                    $updateditemsforuser = array_merge($updateditemsforuser, $updateditemsforcourse);
                    $updateditemsforuser = array_unique($updateditemsforuser);

                    $visiblereditems = array();
                    $newitemsforcourse = $this->get_redballs($course, $starttime);

                    $newitemsforuser = array_merge($newitemsforuser, $newitemsforcourse);

                    $newitemsforuser = array_unique($newitemsforuser);

                    $modinfo = get_fast_modinfo($course);

                    foreach ($modinfo->cms as $cm) {
                        if ($cm->uservisible && !$cm->is_stealth() && in_array($cm->id, $newitemsforuser)) {
                            $visiblereditems[] = $cm->id;
                        }
                        if ($cm->uservisible && !$cm->is_stealth() && in_array($cm->id, $updateditemsforuser) && !in_array($cm->id, $newitemsforuser)) {
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
                            array( 'course' => $course->id )
                        );
                    }
                    $course->updateditemscount = $orangecount;
                    $course->orangeballscountclass = $orangecount > 9 ? 'high' : '';

                    $count = 0;
                    if (!empty($visiblereditems)) {
                        $count = $DB->count_records_sql(
                            "SELECT COUNT(*)
                               FROM {course_modules}
                              WHERE course = :course
                                AND id IN (" . implode(',', $visiblereditems) . ")",
                            array( 'course' => $course->id )
                        );
                    }

                    $course->newitemscount = $count;
                    $course->redballscountclass = $count > 9 ? 'high' : '';
                    if ($orangecount > 9 && $count <= 9) {
                        $course->redballscountclass = 'lower';
                    } else if ($orangecount <= 9 && $count > 9) {
                        $course->orangeballscountclass = 'lower';
                    }

                }

                $instances = enrol_get_instances($course->id, true);
                foreach ($instances as $instance) { // Need to check enrolment methods for self enrol.
                    $plugin = $plugins[$instance->enrol];
                    if (is_enrolled(context_course::instance($course->id))) {
                        $unenrolurl = $plugin->get_unenrolself_link($instance);
                        if ($unenrolurl) {
                            $course->unenrolurl = $unenrolurl->out();
                            break;
                        }
                    }
                    //print_object($course);die();
                }

            }

        }

        if (empty($data['coursesview']['inprogress']) && !empty($data['coursesview']['future']['pages'])) {
            $data['coursesview']['inprogress']['haspages'] = 1;
            $data['coursesview']['inprogress']['pages'] = array();
        }

        if (isset($data['coursesview']['future'])) {
            foreach ($data['coursesview']['future']['pages'] as $page) {
                $courses = $page['courses'];
                foreach ($courses as &$course) {

                    if ($redballsactivated) {
                        $visiblereditems = array();
                        $newitemsforcourse = $this->get_redballs($course, $starttime);

                        $newitemsforuser = array_merge($newitemsforuser, $newitemsforcourse);
                        $newitemsforuser = array_unique($newitemsforuser);

                        foreach ($modinfo->cms as $cm) {
                            if ($cm->uservisible and in_array($cm->id, $newitemsforuser)) {
                                $visiblereditems[] = $cm->id;
                            }
                        }

                        $count = 0;
                        if (!empty($visiblereditems)) {
                            $count = $DB->count_records_sql(
                                "SELECT COUNT(*)
                               FROM {course_modules}
                              WHERE course = :course
                                AND id IN (" . implode(',', $visiblereditems) . ")",
                                array( 'course' => $course->id )
                            );
                        }
                        $course->newitemscount = $count;
                        $course->redballcountclass = $count > 9 ? 'high' : '';
                    }

                    $instances = enrol_get_instances($course->id, true);
                    foreach ($instances as $instance) { // Need to check enrolment methods for self enrol.
                        $plugin = $plugins[$instance->enrol];
                        if (is_enrolled(context_course::instance($course->id))) {
                            $unenrolurl = $plugin->get_unenrolself_link($instance);
                            if ($unenrolurl) {
                                $course->unenrolurl = $unenrolurl->out();
                                break;
                            }

                        }
                    }
                }
            }
        }

        if (isset($data['coursesview']['past'])) {
            foreach ($data['coursesview']['past']['pages'] as $page) {
                $courses = $page['courses'];
                foreach ($courses as &$course) {

                    if ($redballsactivated) {
                        $visiblereditems = array();
                        $newitemsforcourse = $this->get_redballs($course, $starttime);
                        $newitemsforuser = array_merge($newitemsforuser, $newitemsforcourse);
                        $newitemsforuser = array_unique($newitemsforuser);

                        foreach ($modinfo->cms as $cm) {
                            if ($cm->uservisible and in_array($cm->id, $newitemsforuser)) {
                                $visiblereditems[] = $cm->id;
                            }
                        }

                        $count = 0;
                        if (!empty($visiblereditems)) {
                            $count = $DB->count_records_sql(
                                "SELECT COUNT(*)
                               FROM {course_modules}
                              WHERE course = :course
                                AND id IN (" . implode(',', $visiblereditems) . ")",
                                array( 'course' => $course->id )
                            );
                        }

                        $course->newitemscount = $count;
                        $course->redballcountclass = $count > 9 ? 'high' : '';
                    }

                    $instances = enrol_get_instances($course->id, true);
                    foreach ($instances as $instance) { // Need to check enrolment methods for self enrol.
                        $plugin = $plugins[$instance->enrol];
                        if (is_enrolled(context_course::instance($course->id))) {
                            $unenrolurl = $plugin->get_unenrolself_link($instance);
                            if ($unenrolurl) {
                                $course->unenrolurl = $unenrolurl->out();
                                break;
                            }

                        }
                    }
                }
            }
        }

        if (!empty($data['coursesview']['future']) || !empty($data['coursesview']['past'])) {
            $data['displaytabs'] = true;
        } else {
            $data['displaytabs'] = false;
        }
        if ($redballsactivated) {
            set_user_preference('user_redballs', implode(',', array_unique($newitemsforuser)));
            if ($orangeballsactivated) {
                set_user_preference('user_orangeballs', implode(',', array_unique($updateditemsforuser)));
            }
        }

        return $this->render_from_template('block_myoverview/main-alt', $data);
    }}

    public function get_redballs($course, $starttime) {
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

    public function get_orangeballs($course, $starttime) {
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
//print_object($query);
//        print_object($starttime);
//print_object($records);
        $alreadytested = array();
        $redcmids = array();
        foreach ($records as $record) {
            if (in_array($record->contextinstanceid, $alreadytested)) {
                continue;
            } else {
                $alreadytested[] = $record->contextinstanceid;
            }
            $modglossaryid = $DB->get_field('modules', 'id', array('name' => 'glossary'));
            if ($record->eventname == '\mod_glossary\event\entry_updated' || $record->eventname == '\mod_glossary\event\entry_created'){
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

    public function get_redballs_old($course, $starttime) {
        global $DB, $USER;
        $count = 0;
        $query = "SELECT id, contextinstanceid, timecreated
                    FROM {logstore_standard_log}
                   WHERE contextlevel = :contextlevel
                     AND courseid = :courseid
                     AND userid != :userid
                     AND (eventname = '\\\core\\\\event\\\course_module_created'
                         OR eventname = '\\\core\\\\event\\\course_module_updated'
                         OR eventname = '\\\mod_wiki\\\\event\\\page_updated'
                         OR eventname = '\\\mod_quiz\\\\event\\\\edit_page_viewed')
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
}

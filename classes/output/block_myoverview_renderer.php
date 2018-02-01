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
        global $USER;
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
        $selfenrol = false;
//print_object(count($data['coursesview']['inprogress']['pages']));
        foreach ($data['coursesview']['inprogress']['pages'] as $page) {
            //print_object($page['courses']);
            $courses = $page['courses'];
            //$courses = $data['coursesview']['inprogress']['pages'][0]['courses'];
            foreach ($courses as &$course) {
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

        if (empty($data['coursesview']['inprogress']) && !empty($data['coursesview']['future']['pages'])) {
            $data['coursesview']['inprogress']['haspages'] = 1;
            $data['coursesview']['inprogress']['pages'] = array();
        }

        /*foreach ($data['coursesview']['inprogress']['pages'] as &$tosort) {
            usort($tosort['courses'], function($a, $b) {
                return strcmp($a->shortname, $b->shortname);
            });
        }*/
        //print_object($data['coursesview']['inprogress']['pages'][0]['courses']);
        //print_object($data['coursesview']['inprogress']['pages']);

        $lastpage = count($data['coursesview']['inprogress']['pages']) - 1;
        //print_object($lastindex);
        if (isset($data['coursesview']['future'])) {
            foreach ($data['coursesview']['future']['pages'] as $page) {
                $courses = $page['courses'];
                //$courses = $data['coursesview']['future']['pages'][0]['courses'];
                foreach ($courses as &$course) {
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
                    //$data['coursesview']['inprogress']['pages'][$lastpage]['courses'][] = $course;
                }
                //$data['coursesview']['inprogress']['pages'][] = $page;
            }
        }

        if (isset($data['coursesview']['past'])) {
            foreach ($data['coursesview']['past']['pages'] as $page) {
                $courses = $page['courses'];
                //$courses = $data['coursesview']['future']['pages'][0]['courses'];
                foreach ($courses as &$course) {
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
                    //$data['coursesview']['inprogress']['pages'][$lastpage]['courses'][] = $course;
                }
                //$data['coursesview']['inprogress']['pages'][] = $page;
            }
        }

        if (!empty($data['coursesview']['future']) || !empty($data['coursesview']['past'])) {
            $data['displaytabs'] = true;
        } else {
            $data['displaytabs'] = false;
        }

        /*$courses = $data['coursesview']['future']['pages'][0]['courses'];
        foreach ($courses as &$course) {
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
            $data['coursesview']['inprogress']['pages'][$lastpage]['courses'][] = $course;
        }*/
        //print_object($data['coursesview']);
        return $this->render_from_template('block_myoverview/main', $data);
    }
}

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
//require_once($CFG->dirroot . '/theme/receptic/lib.php');

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

        $courselist = array();
        if (isset($data['coursesview']['inprogress'])) {
            foreach ($data['coursesview']['inprogress']['pages'] as $page) {
                $courselist = array_merge($courselist, $page['courses']);
            }
        }
        if (isset($data['coursesview']['future'])) {
            foreach ($data['coursesview']['future']['pages'] as $page) {
                $courselist = array_merge($courselist, $page['courses']);
            }
        }
        if (isset($data['coursesview']['past'])) {
            foreach ($data['coursesview']['past']['pages'] as $page) {
                $courselist = array_merge($courselist, $page['courses']);
            }
        }

        if (!empty($courselist)) {
            $plugins = enrol_get_plugins(true);

            $ballsactivated = get_config('theme_receptic', 'enableballs');

            if ($ballsactivated) {
                list($newitemsforuser, $updateditemsforuser, $starttime) = theme_receptic_init_vars_for_hot_items_computing();
            }

            foreach ($courselist as &$course) {
                $coursecontext = context_course::instance($course->id);
                if (has_capability('moodle/course:update', $coursecontext)) {
                    $course->allowtogglevisibility = true;
                    $course->togglevisibilityurl = $CFG->wwwroot . '/theme/receptic/utils.php';
                    $course->sesskey = sesskey();
                }
                if ($ballsactivated) {

                    $newitemsforuser = theme_receptic_compute_redballs($course, $starttime, $newitemsforuser);
                    $updateditemsforuser = theme_receptic_compute_orangeballs($course, $starttime, $updateditemsforuser);

                    list($redcount, $orangecount) = theme_receptic_get_visible_balls_count($course, $newitemsforuser, $updateditemsforuser);

                    $course->newitemscount = $redcount;
                    $course->redballscountclass = $redcount > 9 ? 'high' : '';
                    $course->updateditemscount = $orangecount;
                    $course->orangeballscountclass = $orangecount > 9 ? 'high' : '';
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

        if (!empty($data['coursesview']['future']) || !empty($data['coursesview']['past'])) {
            $data['displaytabs'] = true;
        } else {
            $data['displaytabs'] = false;
        }

        return $this->render_from_template('block_myoverview/main-alt', $data);
    }}
}

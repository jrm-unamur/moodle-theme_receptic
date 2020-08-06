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
 * Overridden renderer for myoverview block
 *
 * @package    theme_receptic
 * @author     Jean-Roch Meurisse
 * @copyright  2016 - Cellule TICE - Unversite de Namur
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/blocks/myoverview/classes/output/renderer.php');
require_once($CFG->dirroot . '/course/classes/external/course_summary_exporter.php');
require_once($CFG->dirroot . '/theme/receptic/classes/external/course_summary_exporter.php');

/**
 * myoverview block renderer class
 *
 * @package    theme_receptic
 * @author     Jean-Roch Meurisse
 * @copyright  2018 - Cellule TICE - Unversite de Namur
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class theme_receptic_block_myoverview_renderer extends \block_myoverview\output\renderer {

    /**
     * Return the main content for the block overview.
     *
     * @param \block_myoverview\output\main $main The main renderable
     * @return string HTML string
     */
    public function render_main(\block_myoverview\output\main $main) {
        global $CFG, $USER;

        $data = $main->export_for_template($this);

        // Add course button on dashboard.
        $addcoursebutton = get_config('theme_receptic', 'addcoursebutton');
        if ($addcoursebutton) {
            $createcourseplugin = core_plugin_manager::instance()
                ->get_plugin_info(get_config('theme_receptic', 'localcreatecourseplugin'));
            if ($createcourseplugin
                    && has_capability('local/' . $createcourseplugin->name . ':create', context_system::instance())) {
                $data['urls']['addcourse'] = new moodle_url('/local/' . $createcourseplugin->name . '/index.php');
                $data['cancreatecourse'] = true;
            } else if (has_capability('moodle/course:create', context_system::instance())) {
                $data['urls']['addcourse'] = new moodle_url('/course/edit.php?category=1&returnto=topcat');
                $data['cancreatecourse'] = true;
            }
        }

        // Bulk enrolme button on dashboard.
        $bulkenrolmebutton = get_config('theme_receptic', 'bulkenrolme');
        if ($bulkenrolmebutton) {
            $bulkenrolmeplugin = core_plugin_manager::instance()
                ->get_plugin_info(get_config('theme_receptic', 'bulkenrolmeplugin'));
            if ($bulkenrolmeplugin) {
                $emailpattern = get_config('theme_receptic', 'bulkenrolemailpattern');
                $filepath = $CFG->dirroot . '/' .
                        $bulkenrolmeplugin->type . '/' .
                        $bulkenrolmeplugin->name . '/' .
                        get_config('theme_receptic', 'bulkenrolmefile');
                if (file_exists($filepath)
                        && (empty($emailpattern) || substr_count($USER->email, $emailpattern) )) {
                    $data['urls']['enrolme'] = new moodle_url('/enrol/noe/enrolnoecourses.php');
                    $data['noestudent'] = true;
                }
            }
        }
        
        $data['allowcoursesorting'] = get_config('theme_receptic', 'allowcoursesorting');

        return $this->render_from_template('block_myoverview/main', $data);
    }
}

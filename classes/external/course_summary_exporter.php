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
use core\external\exporter;
use core_course\external\course_summary_exporter;

/**
 * Class replacing core course_summary_exporter for exporting a course summary from an stdClass.
 * Developped to add functionality to myoverview.
 *
 * @author     Jean-Roch Meurisse
 * @copyright  2018 - Cellule TICE - Unversite de Namur
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class theme_receptic_course_summary_exporter extends exporter {

    /**
     * Constructor - saves the persistent object, and the related objects.
     *
     * @param mixed $data - Either an stdClass or an array of values.
     * @param array $related - An optional list of pre-loaded objects related to this object.
     */
    public function __construct($data, $related = array()) {
        if (!array_key_exists('isfavourite', $related)) {
            $related['isfavourite'] = false;
        }

        parent::__construct($data, $related);
    }

    protected static function define_related() {
        // We cache the context so it does not need to be retrieved from the course.
        return array(
            'context' => '\\context',
            'isfavourite' => 'bool?',
            'newitemscount' => 'int?',
            'redballscountclass' => 'string?',
            'updateditemscount' => 'int?',
            'orangeballscountclass' => 'string?');
    }

    protected function get_other_values(renderer_base $output) {
        global $CFG;
        $courseimage = course_summary_exporter::get_course_image($this->data);
        if (!$courseimage) {
            $courseimage = course_summary_exporter::get_course_pattern($this->data);
        }
        $progress = course_summary_exporter::get_course_progress($this->data);
        $hasprogress = false;
        if ($progress === 0 || $progress > 0) {
            $hasprogress = true;
        }
        $progress = floor($progress);

        return array(
            'fullnamedisplay' => get_course_display_name_for_list($this->data),
            'viewurl' => (new moodle_url('/course/view.php', array('id' => $this->data->id)))->out(false),
            'courseimage' => $courseimage,
            'progress' => $progress,
            'hasprogress' => $progress && $hasprogress && get_config('theme_receptic', 'showprogress'),
            'isfavourite' => $this->related['isfavourite'],
            'hidden' => boolval(get_user_preferences('block_myoverview_hidden_course_' . $this->data->id, 0)),
            'showshortname' => $CFG->courselistshortnames ? true : false,
            'unenrolurl' => '',
            'enrolid' => self::get_enrolid($this->data),
            'allowtogglevisibility' => has_capability('moodle/course:visibility', context_course::instance($this->data->id))
                && get_config('theme_receptic', 'togglecoursevisibility'),
            'newitemscount' => $this->related['newitemscount'],
            'redballscountclass' => $this->related['redballscountclass'],
            'updateditemscount' => $this->related['updateditemscount'],
            'orangeballscountclass' => $this->related['orangeballscountclass']
        );
    }

    protected static function get_enrolid($course) {
        if (get_config('theme_receptic', 'unenrolme')) {
            $plugins = enrol_get_plugins(true);
            $instances = enrol_get_instances($course->id, true);
            foreach ($instances as $instance) { // Need to check enrolment methods for self enrol.
                $plugin = $plugins[$instance->enrol];
                if (is_enrolled(context_course::instance($course->id))) {

                    $unenrolurl = $plugin->get_unenrolself_link($instance);
                    if ($unenrolurl) {
                        return $instance->id;
                    }
                }
            }
        }
        return 0;
    }

    public static function define_properties() {
        global $DB;
        $tmp = new stdClass();
        $tmp->trace = implode(', ', array_keys(course_summary_exporter::define_properties()));
        $DB->insert_record('webcampus_trace', $tmp);
        return array_merge(course_summary_exporter::define_properties(), [
            'visible' => array(
                'type' => PARAM_BOOL
            )
        ]);
    }

    public static function define_other_properties() {

        return array_merge(course_summary_exporter::define_other_properties(), [
            'unenrolurl' => array(
                'type' => PARAM_URL
            ),
            'enrolid' => array(
                'type' => PARAM_INT
            ),
            'allowtogglevisibility' => array(
                'type' => PARAM_BOOL
            ),
            'newitemscount' => array(
                'type' => PARAM_INT
            ),
            'redballscountclass' => array(
                'type' => PARAM_ALPHA
            ),
            'updateditemscount' => array(
                'type' => PARAM_INT
            ),
            'orangeballscountclass' => array(
                'type' => PARAM_ALPHA
            )
        ]);
    }
}
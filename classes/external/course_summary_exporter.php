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
 * Class for exporting a course summary from an stdClass. Developed to add functionality to myoverview.
 *
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

    /**
     * Returns a list of objects that are related to this persistent.
     *
     * Only objects listed here can be cached in this object.
     *
     * The class name can be suffixed:
     * - with [] to indicate an array of values.
     * - with ? to indicate that 'null' is allowed.
     *
     * @return array of 'propertyname' => array('type' => classname, 'required' => true)
     */
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

    /**
     * Get the additional values to inject while exporting.
     *
     * These are additional generated values that are not passed in through $data
     * to the exporter. For a persistent exporter - these are generated values that
     * do not exist in the persistent class. For your convenience the format_text or
     * format_string functions do not need to be applied to PARAM_TEXT fields,
     * it will be done automatically during export.
     *
     * These values are only used when returning data via self::export(),
     * they are not used when generating any of the different external structures.
     *
     * Note: These must be defined in self::define_other_properties().
     *
     * @param renderer_base $output The renderer.
     * @return array Keys are the property names, values are their values.
     */
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
        $coursecategory = \core_course_category::get($this->data->category, MUST_EXIST, true);
        return array(
            'fullnamedisplay' => get_course_display_name_for_list($this->data),
            'viewurl' => (new moodle_url('/course/view.php', array('id' => $this->data->id)))->out(false),
            'courseimage' => $courseimage,
            'progress' => $progress,
            'hasprogress' => $progress && $hasprogress && get_config('theme_receptic', 'showprogress'),
            'isfavourite' => $this->related['isfavourite'],
            'hidden' => boolval(get_user_preferences('block_myoverview_hidden_course_' . $this->data->id, 0)),
            'showshortname' => $CFG->courselistshortnames ? true : false,
            'coursecategory' => $coursecategory->name,
            'unenrolurl' => '',
            'enrolid' => self::get_enrolid($this->data),
            'allowtogglevisibility' => has_capability('moodle/course:visibility', context_course::instance($this->data->id))
                && get_config('theme_receptic', 'togglecoursevisibility'),
            'allowremovecoursesfromdisplay' => get_config('theme_receptic', 'allowremovecoursesfromdisplay'),
            'newitemscount' => $this->related['newitemscount'],
            'redballscountclass' => $this->related['redballscountclass'],
            'updateditemscount' => $this->related['updateditemscount'],
            'orangeballscountclass' => $this->related['orangeballscountclass']
        );
    }


    /**
     * Gets the enrolment id of the current user for the current course to display an unerol link on the dashboard.
     *
     * @param stdClass $course The current course
     * @return int The enrolment id of the current user for the current course
     * @throws dml_exception
     */
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

    /**
     * Return the list of properties.
     *
     * The format of the array returned by this method has to match the structure
     * defined in \core\persistent::define_properties(). Howewer you can
     * add a new attribute "description" to describe the parameter for documenting the API.
     *
     * Note that the type PARAM_TEXT should ONLY be used for strings which need to
     * go through filters (multilang, etc...) and do not have a FORMAT_* associated
     * to them. Typically strings passed through to format_string().
     *
     * Other filtered strings which use a FORMAT_* constant (hear used with format_text)
     * must be defined as PARAM_RAW.
     *
     * @return array
     */
    public static function define_properties() {
        /*return array_merge(course_summary_exporter::define_properties(), [
            'visible' => array(
                'type' => PARAM_BOOL
            )
        ]);*/
        return course_summary_exporter::define_properties();
    }

    /**
     * Return the list of additional properties used only for display.
     *
     * Additional properties are only ever used for the read structure, and during
     * export of the persistent data.
     *
     * The format of the array returned by this method has to match the structure
     * defined in \core\persistent::define_properties(). The display properties
     * can however do some more fancy things. They can define 'multiple' => true to wrap
     * values in an external_multiple_structure automatically - or they can define the
     * type as a nested array of more properties in order to generate a nested
     * external_single_structure.
     *
     * You can specify an array of values by including a 'multiple' => true array value. This
     * will result in a nested external_multiple_structure.
     * E.g.
     *
     *       'arrayofbools' => array(
     *           'type' => PARAM_BOOL,
     *           'multiple' => true
     *       ),
     *
     * You can return a nested array in the type field, which will result in a nested external_single_structure.
     * E.g.
     *      'competency' => array(
     *          'type' => competency_exporter::read_properties_definition()
     *       ),
     *
     * Other properties can be specifically marked as optional, in which case they do not need
     * to be included in the export in self::get_other_values(). This is useful when exporting
     * a substructure which cannot be set as null due to webservices protocol constraints.
     * E.g.
     *      'competency' => array(
     *          'type' => competency_exporter::read_properties_definition(),
     *          'optional' => true
     *       ),
     *
     * @return array
     */
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
            'allowremovecoursesfromdisplay' => array(
                'type' => PARAM_BOOL
            ),
            'newitemscount' => array(
                'type' => PARAM_INT,
                'optional' => true
            ),
            'redballscountclass' => array(
                'type' => PARAM_ALPHA,
                'optional' => true
            ),
            'updateditemscount' => array(
                'type' => PARAM_INT,
                'optional' => true
            ),
            'orangeballscountclass' => array(
                'type' => PARAM_ALPHA,
                'optional' => true
            )
        ]);
    }
}
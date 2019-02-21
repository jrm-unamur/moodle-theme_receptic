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

require_once($CFG->libdir . '/externallib.php');
require_once($CFG->dirroot . '/course/classes/external/course_summary_exporter.php');
require_once('classes/external/course_summary_exporter.php');

/**
 * Created by PhpStorm.
 * User: jmeuriss
 * Date: 4/12/18
 * Time: 15:18
 */
class theme_receptic_external extends external_api {

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function get_enrolled_courses_by_timeline_classification_parameters() {
        return new external_function_parameters(
            array(
                'classification' => new external_value(PARAM_ALPHA, 'future, inprogress, or past'),
                'limit' => new external_value(PARAM_INT, 'Result set limit', VALUE_DEFAULT, 0),
                'offset' => new external_value(PARAM_INT, 'Result set offset', VALUE_DEFAULT, 0),
                'sort' => new external_value(PARAM_TEXT, 'Sort string', VALUE_DEFAULT, null)
            )
        );
    }

    /**
     * Get courses matching the given timeline classification.
     *
     * NOTE: The offset applies to the unfiltered full set of courses before the classification
     * filtering is done.
     * E.g.
     * If the user is enrolled in 5 courses:
     * c1, c2, c3, c4, and c5
     * And c4 and c5 are 'future' courses
     *
     * If a request comes in for future courses with an offset of 1 it will mean that
     * c1 is skipped (because the offset applies *before* the classification filtering)
     * and c4 and c5 will be return.
     *
     * @param  string $classification past, inprogress, or future
     * @param  int $limit Result set limit
     * @param  int $offset Offset the full course set before timeline classification is applied
     * @param  string $sort SQL sort string for results
     * @return array list of courses and warnings
     * @throws  invalid_parameter_exception
     */
    public static function get_enrolled_courses_by_timeline_classification(
        string $classification,
        int $limit = 0,
        int $offset = 0,
        string $sort = null
    ) {
        global $CFG, $PAGE, $USER;
        require_once($CFG->dirroot . '/course/lib.php');
        $params = self::validate_parameters(self::get_enrolled_courses_by_timeline_classification_parameters(),
            array(
                'classification' => $classification,
                'limit' => $limit,
                'offset' => $offset,
                'sort' => $sort,
            )
        );

        $classification = $params['classification'];
        $limit = $params['limit'];
        $offset = $params['offset'];
        $sort = $params['sort'];

        switch($classification) {
            case COURSE_TIMELINE_ALL:
                break;
            case COURSE_TIMELINE_PAST:
                break;
            case COURSE_TIMELINE_INPROGRESS:
                break;
            case COURSE_TIMELINE_FUTURE:
                break;
            case COURSE_FAVOURITES:
                break;
            case COURSE_TIMELINE_HIDDEN:
                break;
            default:
                throw new invalid_parameter_exception('Invalid classification');
        }

        self::validate_context(context_user::instance($USER->id));

        $requiredproperties = array_merge(\core_course\external\course_summary_exporter::define_properties(),
            theme_receptic_course_summary_exporter::define_properties());

        $fields = join(',', array_keys($requiredproperties));

        $hiddencourses = get_hidden_courses_on_timeline();
        $courses = [];

        // If the timeline requires the hidden courses then restrict the result to only $hiddencourses else exclude.
        if ($classification == COURSE_TIMELINE_HIDDEN) {
            $courses = course_get_enrolled_courses_for_logged_in_user(0, $offset, $sort, $fields,
                COURSE_DB_QUERY_LIMIT, $hiddencourses);
        } else {
            $courses = course_get_enrolled_courses_for_logged_in_user(0, $offset, $sort, $fields,
                COURSE_DB_QUERY_LIMIT, [], $hiddencourses);
        }

        $favouritecourseids = [];
        $ufservice = \core_favourites\service_factory::get_service_for_user_context(\context_user::instance($USER->id));
        $favourites = $ufservice->find_favourites_by_type('core_course', 'courses');

        if ($favourites) {
            $favouritecourseids = array_map(
                function($favourite) {
                    return $favourite->itemid;
                }, $favourites);
        }

        if ($classification == COURSE_FAVOURITES) {
            list($filteredcourses, $processedcount) = course_filter_courses_by_favourites(
                $courses,
                $favouritecourseids,
                $limit
            );
        } else {
            list($filteredcourses, $processedcount) = course_filter_courses_by_timeline_classification(
                $courses,
                $classification,
                $limit
            );
        }

        $renderer = $PAGE->get_renderer('core');
        $ballsactivated = get_config('theme_receptic', 'enableballs');

        if ($ballsactivated) {
            list($newitemsforuser, $updateditemsforuser, $starttime) = theme_receptic_init_vars_for_hot_items_computing();
        }
        $formattedcourses = array_map(function($course) use ($renderer, $favouritecourseids, $newitemsforuser,
                $updateditemsforuser, $starttime, $ballsactivated) {
            context_helper::preload_from_record($course);
            $context = context_course::instance($course->id);
            $isfavourite = false;
            if (in_array($course->id, $favouritecourseids)) {
                $isfavourite = true;
            }
            if ($ballsactivated) {

                $newitemsforuser = theme_receptic_compute_redballs($course, $starttime, $newitemsforuser);
                $updateditemsforuser = theme_receptic_compute_orangeballs($course, $starttime, $updateditemsforuser);

                list($redcount, $orangecount) =
                    theme_receptic_get_visible_balls_count($course, $newitemsforuser, $updateditemsforuser);

                $redballsclass = $redcount > 9 ? 'high' : '';
                $orangeballsclass = $orangecount > 9 ? 'high' : '';
            }

            $themeexporter = new theme_receptic_course_summary_exporter($course, [
                'context' => $context,
                'isfavourite' => $isfavourite,
                'newitemscount' => $redcount,
                'redballscountclass' => $redballsclass,
                'updateditemscount' => $orangecount,
                'orangeballscountclass' => $orangeballsclass
            ]);
            return $themeexporter->export($renderer);
        }, $filteredcourses);

        return [
            'courses' => $formattedcourses,
            'nextoffset' => $offset + $processedcount
        ];
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     */
    public static function get_enrolled_courses_by_timeline_classification_returns() {
        return new external_single_structure(
            array(
                'courses' => new external_multiple_structure(
                        theme_receptic_course_summary_exporter::get_read_structure(), 'Course'
                ),
                'nextoffset' => new external_value(PARAM_INT, 'Offset for the next request')
            )
        );
    }

    public static function change_course_visibility_parameters() {
        return new external_function_parameters(
            array(
                'id' => new external_value(PARAM_INT, 'course ID'),
                'visible' => new external_value(PARAM_BOOL, 'visibility status')
            )
        );
    }

    public static function change_course_visibility(int $id, bool $visible) {
        global $DB;

        $params = self::validate_parameters(self::change_course_visibility_parameters(),
            array(
                'id' => $id,
                'visible' => $visible,
            )
        );

        $warnings = [];

        $record = new stdClass();
        $record->id = $id;
        $record->visible = $visible;
        $DB->update_record('course', $record);

        return [
            'warnings' => $warnings
        ];
    }

    public static function change_course_visibility_returns() {
        return new external_single_structure(
            array(
                'warnings' => new external_warnings()
            )
        );
    }

    public static function unenrolme_parameters() {
        return new external_function_parameters(
            array(
                'id' => new external_value(PARAM_INT, 'course ID'),
                'instanceid' => new external_value(PARAM_INT, 'enrol ID')
            )
        );
    }

    public static function unenrolme(int $id, int $instanceid) {
        global $DB, $USER;

        $params = self::validate_parameters(self::unenrolme_parameters(),
            array(
                'id' => $id,
                'instanceid' => $instanceid,
            )
        );

        $warnings = [];

        $instance = $DB->get_record('enrol', array('id' => $params['instanceid']), '*', MUST_EXIST);
        $course = $DB->get_record('course', array('id' => $params['id']), '*', MUST_EXIST);

        $plugin = enrol_get_plugin($instance->enrol);

        $plugin->unenrol_user($instance, $USER->id);

        return [
            'warnings' => $warnings
        ];
    }

    public static function unenrolme_returns() {
        return new external_single_structure(
            array(
                'warnings' => new external_warnings()
            )
        );
    }
}
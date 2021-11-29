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
 * Event observers used in Receptic theme.
 *
 * @package    theme_receptic
 * @author     Jean-Roch Meurisse
 * @copyright  2016 - Cellule TICE - Unversite de Namur
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Event observer for theme_receptic.
 * @author     Jean-Roch Meurisse
 * @copyright  2016 - Cellule TICE - Unversite de Namur
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class theme_receptic_observer {

    /**
     * Triggered via course_module_viewed events.
     * Updates current user red and orange balls.
     *
     * @param \core\event\base $event
     */
    public static function course_module_viewed(core\event\base $event) {
        global $DB;
        $eventdata = $event->get_data();
        $neweventid = $DB->insert_record('theme_receptic_filtered_log', $event->get_data());
        $eventdata['neweventid'] = $neweventid;
        $statement = "eventname = :eventname
                         AND component = :component
                         AND action = :action
                         AND target = :target
                         AND crud = :crud
                         AND contextid = :contextid
                         AND contextlevel = :contextlevel
                         AND contextinstanceid = :contextinstanceid
                         AND userid = :userid
                         AND courseid = :courseid
                         AND id != :neweventid
                         ";

        $DB->delete_records_select('theme_receptic_filtered_log', $statement, $eventdata);
        $hotusermodules = explode(',', get_user_preferences('user_redballs'));
        $warmusermodules = explode(',', get_user_preferences('user_orangeballs'));
        if (in_array($eventdata['contextinstanceid'], $hotusermodules)
                || in_array($eventdata['contextinstanceid'], $warmusermodules)) {
            $hotusermodules = array_diff($hotusermodules, [$eventdata['contextinstanceid']]);
            $warmusermodules = array_diff($warmusermodules, [$eventdata['contextinstanceid']]);
            set_user_preference('user_redballs', implode(',', $hotusermodules));
            set_user_preference('user_orangeballs', implode(',', $warmusermodules));
        }
    }

    /**
     * Triggered via course_viewed event.
     * Updates current user red and orange balls.
     *
     * @param \core\event\course_viewed $event
     */
    public static function course_viewed(core\event\course_viewed $event) {
        global $DB;
        $eventdata = $event->get_data();
        // Log event in filtered table.
        $neweventid = $DB->insert_record('theme_receptic_filtered_log', $eventdata);
        $eventdata['neweventid'] = $neweventid;
        $statement = "eventname = :eventname
                         AND component = :component
                         AND action = :action
                         AND target = :target
                         AND crud = :crud
                         AND contextid = :contextid
                         AND contextlevel = :contextlevel
                         AND contextinstanceid = :contextinstanceid
                         AND userid = :userid
                         AND courseid = :courseid
                         AND id != :neweventid
                         ";

        $DB->delete_records_select('theme_receptic_filtered_log', $statement, $eventdata);

        $modlabelid = $DB->get_field('modules', 'id', array('name' => 'label'));
        $hotusermodules = explode(',', get_user_preferences('user_redballs'));
        $warmusermodules = explode(',', get_user_preferences('user_orangeballs'));
        $labels = $DB->get_records('course_modules', array('module' => $modlabelid, 'course' => $eventdata['courseid']));
        $hotusermodules = array_diff($hotusermodules, array_keys($labels));
        $warmusermodules = array_diff($warmusermodules, array_keys($labels));
        set_user_preference('user_redballs', implode(',', $hotusermodules));
        set_user_preference('user_orangeballs', implode(',', $warmusermodules));
    }

    /**
     * Copies selected log events into theme_receptic_filtered_log table.
     *
     * @param \core\event\base $event
     */
    public static function store(\core\event\base $event) {
        global $DB;
        $eventdata = $event->get_data();
        $eventdata['other'] = serialize($eventdata['other']);
        $neweventid = $DB->insert_record('theme_receptic_filtered_log', $eventdata);
        $eventdata['neweventid'] = $neweventid;
        if ($event->eventname == '\core\event\course_module_updated') {
            $statement = "eventname = :eventname
                         AND component = :component
                         AND action = :action
                         AND target = :target
                         AND crud = :crud
                         AND contextid = :contextid
                         AND contextlevel = :contextlevel
                         AND contextinstanceid = :contextinstanceid
                         AND userid = :userid
                         AND courseid = :courseid
                         AND id != :neweventid
                         ";

            $DB->delete_records_select('theme_receptic_filtered_log', $statement, $eventdata);
        }
    }
}

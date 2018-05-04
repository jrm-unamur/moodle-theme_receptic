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

class theme_receptic_observer {
    public static function user_loggedout(core\event\base $event) {
        global $DB;
        $eventdata = $event->get_data();
        $DB->delete_records_select('user_preferences', $DB->sql_like('name', ':name') . ' AND userid=:userid ',
            array( 'name' => 'sections-toggle-%', 'userid' => $eventdata['userid']));
    }

    /*public static function user_created(core\event\base $event) {
        global $DB;
        $eventdata = $event->get_data();
        $user = $DB->get_record('user', array('id' => $eventdata['objectid']));
        $roles = get_archetype_roles('coursecreator');
        $sitecontext = context_system::instance();
        $creatorrole = array_shift($roles);
        if ($creatorrole !== false and noe_is_teacher($user->username)) {
           // role_assign($creatorrole->id, $user->id, $sitecontext->id);
        }
    }*/

    public static function course_module_viewed(core\event\base $event) {
        global $DB;
        $eventdata = $event->get_data();

        $hotusermodules = explode(',', get_user_preferences('user_redballs'));
        $warmusermodules = explode(',', get_user_preferences('user_orangeballs'));
        if (in_array($eventdata['contextinstanceid'], $hotusermodules) || in_array($eventdata['contextinstanceid'], $warmusermodules)) {
            $hotusermodules = array_diff($hotusermodules, [$eventdata['contextinstanceid']]);
            $warmusermodules = array_diff($warmusermodules, [$eventdata['contextinstanceid']]);
            set_user_preference('user_redballs', implode(',', $hotusermodules));
            set_user_preference('user_orangeballs', implode(',', $warmusermodules));
        }
    }

    public static function course_viewed(core\event\base $event) {
        global $DB;
        $eventdata = $event->get_data();
        //print_object($eventdata);die();
        $modlabelid = $DB->get_field('modules', 'id', array('name' => 'label'));
        $hotusermodules = explode(',', get_user_preferences('user_redballs'));
        $warmusermodules = explode(',', get_user_preferences('user_orangeballs'));
        $labels = $DB->get_records('course_modules', array('module' => $modlabelid, 'course' => $eventdata['courseid']));//print_object(array_keys($labels));print_object($hotusermodules);
        //print_object($labels);
        $hotusermodules = array_diff($hotusermodules, array_keys($labels));
        $warmusermodules = array_diff($warmusermodules, array_keys($labels));
        set_user_preference('user_redballs', implode(',', $hotusermodules));
        set_user_preference('user_orangeballs', implode(',', $warmusermodules));
        /*if (in_array($eventdata['contextinstanceid'], $hotusermodules)) {
            $hotusermodules = array_diff($hotusermodules, [$eventdata['contextinstanceid']]);

        }*/

    }
}

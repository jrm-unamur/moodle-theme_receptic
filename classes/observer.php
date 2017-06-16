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

class theme_receptic_observer
{
    public static function user_loggedout(core\event\base $event) {
        global $DB;
        $eventdata = $event->get_data();
        $DB->delete_records_select('user_preferences', $DB->sql_like('name', ':name') . ' AND userid=:userid ',
            array( 'name' => 'sections-toggle-%', 'userid' => $eventdata['userid']));
    }
}
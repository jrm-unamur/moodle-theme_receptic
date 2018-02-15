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
 * Enrol config manipulation script.
 *
 * @package    core
 * @subpackage enrol
 * @copyright  2010 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

//define('NO_OUTPUT_BUFFERING', true);

require_once('../../config.php');

global $DB, $USER, $COURSE;

$action  = required_param('action', PARAM_ALPHANUMEXT);
$courseid = required_param('courseid', PARAM_INT);

$PAGE->set_url('/theme/receptic/utils.php');
$PAGE->set_context(context_system::instance());

require_login();
require_capability('moodle/course:update', context_course::instance($courseid));
require_sesskey();

//$enabled = enrol_get_plugins(true);
//$all     = enrol_get_plugins(false);

$return = new moodle_url('/my');

//$syscontext = context_system::instance();

switch ($action) {
    case 'show':
        $course = $DB->get_record('course', array('id' => $courseid));
        $trace = new stdClass();
        $trace->trace = 'Cours ' . $course->shortname . ' (' . $course->id . ') rendu visible par ' . $USER->firstname . ' ' . $USER->lastname;
        $DB->insert_record('webcampus_trace', $trace);
        $record = new stdClass();
        $record->id = $courseid;
        $record->visible = 1;
        $DB->update_record('course', $record);
        break;

    case 'hide':
        $course = $DB->get_record('course', array('id' => $courseid));
        $trace = new stdClass();
        $trace->trace = 'Cours ' . $course->shortname . ' (' . $course->id . ') masqué par ' . $USER->firstname . ' ' . $USER->lastname;
        $DB->insert_record('webcampus_trace', $trace);
        $record = new stdClass();
        $record->id = $courseid;
        $record->visible = 0;
        $DB->update_record('course', $record);
        break;
}

redirect($return);
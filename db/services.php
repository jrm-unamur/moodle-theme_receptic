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

$functions = array(
    'theme_receptic_get_enrolled_courses_by_timeline_classification' => array(
        'classname' => 'theme_receptic_external',
        'methodname' => 'get_enrolled_courses_by_timeline_classification',
        'classpath' => 'theme/receptic/externallib.php',
        'description' => 'tagadagada',
        'type' => 'write',
        'ajax' => true
    ),
    'theme_receptic_change_course_visibility' => array(
        'classname' => 'theme_receptic_external',
        'methodname' => 'change_course_visibility',
        'classpath' => 'theme/receptic/externallib.php',
        'description' => 'tagaditagada',
        'type' => 'write',
        'ajax' => true
    ),
    'theme_receptic_unenrolme' => array(
        'classname' => 'theme_receptic_external',
        'methodname' => 'unenrolme',
        'classpath' => 'theme/receptic/externallib.php',
        'description' => 'tagaditagadatsointsoin',
        'type' => 'write',
        'ajax' => true
    ),
);
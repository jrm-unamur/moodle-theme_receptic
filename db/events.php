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

$observers = array(

    array(
        'eventname' => '\mod_resource\event\course_module_viewed',
        'callback' => 'theme_receptic_observer::course_module_viewed'
    ),

    array(
        'eventname' => '\mod_assign\event\submission_status_viewed',
        'callback' => 'theme_receptic_observer::course_module_viewed'
    ),

    array(
        'eventname' => '\mod_forum\event\course_module_viewed',
        'callback' => 'theme_receptic_observer::course_module_viewed'
    ),

    array(
        'eventname' => '\mod_workshop\event\course_module_viewed',
        'callback' => 'theme_receptic_observer::course_module_viewed'
    ),

    array(
        'eventname' => '\mod_data\event\course_module_viewed',
        'callback' => 'theme_receptic_observer::course_module_viewed'
    ),

    array(
        'eventname' => '\mod_chat\event\course_module_viewed',
        'callback' => 'theme_receptic_observer::course_module_viewed'
    ),

    array(
        'eventname' => '\mod_choicegroup\event\course_module_viewed',
        'callback' => 'theme_receptic_observer::course_module_viewed'
    ),

    array(
        'eventname' => '\mod_choice\event\course_module_viewed',
        'callback' => 'theme_receptic_observer::course_module_viewed'
    ),

    array(
        'eventname' => '\mod_feedback\event\course_module_viewed',
        'callback' => 'theme_receptic_observer::course_module_viewed'
    ),

    array(
        'eventname' => '\mod_quiz\event\course_module_viewed',
        'callback' => 'theme_receptic_observer::course_module_viewed'
    ),

    array(
        'eventname' => '\mod_glossary\event\course_module_viewed',
        'callback' => 'theme_receptic_observer::course_module_viewed'
    ),

    array(
        'eventname' => '\mod_lti\event\course_module_viewed',
        'callback' => 'theme_receptic_observer::course_module_viewed'
    ),

    array(
        'eventname' => '\mod_scheduler\event\booking_form_viewed',
        'callback' => 'theme_receptic_observer::course_module_viewed'
    ),

    array(
        'eventname' => '\mod_scorm\event\course_module_viewed',
        'callback' => 'theme_receptic_observer::course_module_viewed'
    ),

    array(
        'eventname' => '\mod_survey\event\course_module_viewed',
        'callback' => 'theme_receptic_observer::course_module_viewed'
    ),

    array(
        'eventname' => '\mod_wiki\event\course_module_viewed',
        'callback' => 'theme_receptic_observer::course_module_viewed'
    ),

    array(
        'eventname' => '\mod_cobra\event\course_module_viewed',
        'callback' => 'theme_receptic_observer::course_module_viewed'
    ),

    array(
        'eventname' => '\mod_folder\event\course_module_viewed',
        'callback' => 'theme_receptic_observer::course_module_viewed'
    ),

    array(
        'eventname' => '\mod_book\event\course_module_viewed',
        'callback' => 'theme_receptic_observer::course_module_viewed'
    ),

    array(
        'eventname' => '\mod_page\event\course_module_viewed',
        'callback' => 'theme_receptic_observer::course_module_viewed'
    ),

    array(
        'eventname' => '\mod_imscp\event\course_module_viewed',
        'callback' => 'theme_receptic_observer::course_module_viewed'
    ),

    array(
        'eventname' => '\mod_url\event\course_module_viewed',
        'callback' => 'theme_receptic_observer::course_module_viewed'
    ),

    array(
        'eventname' => '\core\event\course_viewed',
        'callback' => 'theme_receptic_observer::course_viewed'
    ),

    array(
        'eventname' => '\mod_animals\event\course_module_viewed',
        'callback' => 'theme_receptic_observer::course_module_viewed'
    ),

);

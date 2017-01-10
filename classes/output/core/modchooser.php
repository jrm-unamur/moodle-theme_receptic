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
 * @package    
 * @author     Jean-Roch Meurisse
 * @copyright  2016 - Cellule TICE - Unversite de Namur
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_receptic\output\core;
defined('MOODLE_INTERNAL') || die();

use core\output\chooser;
use core\output\chooser_section;
use core_course\output\modchooser_item;
use context_course;
use lang_string;
use moodle_url;
use stdClass;

global $CFG;
//require_once($CFG->dirroot . '/course/classes/output/modchooser.php');

class modchooser extends \core_course\output\modchooser {
    public function __construct(stdClass $course, array $modules) {
        $this->course = $course;

        $sections = [];
        $context = context_course::instance($course->id);

        // Preferred activities and resources.
        $preferredmodules = array();
        $advancedmodules = array();
        foreach ($modules as $module) {
            if (get_config('theme_receptic', $module->name . 'inshortlist')) {
            //if ($settings->{$module->name . 'inshortlist'}) {
                $preferredmodules[] = $module;
            } else {
                $advancedmodules[] = $module;
            }
        }

        // Preferred modules section.
        if (count($preferredmodules)) {
            $sections[] = new chooser_section('mainmodules', new lang_string('mainmodules', 'theme_receptic'),
                array_map(function($module) use ($context) {
                    return new modchooser_item($module, $context);
                }, $preferredmodules)
            );
        }

        // Activities.
        $activities = array_filter($advancedmodules, function($mod) {
            return ($mod->archetype !== MOD_ARCHETYPE_RESOURCE && $mod->archetype !== MOD_ARCHETYPE_SYSTEM);
        });
        if (count($activities)) {
            $sections[] = new chooser_section('otheractivities', new lang_string('otheractivities', 'theme_receptic'),
                array_map(function($module) use ($context) {
                    return new modchooser_item($module, $context);
                }, $activities)
            );
        }

        $resources = array_filter($advancedmodules, function($mod) {
            return ($mod->archetype === MOD_ARCHETYPE_RESOURCE);
        });
        if (count($resources)) {
            $sections[] = new chooser_section('otherresources', new lang_string('otherresources', 'theme_receptic'),
                array_map(function($module) use ($context) {
                    return new modchooser_item($module, $context);
                }, $resources)
            );
        }

        $actionurl = new moodle_url('/course/jumpto.php');
        $title = new lang_string('addresourceoractivity');
        chooser::__construct($actionurl, $title, $sections, 'jumplink');

        $this->set_instructions(new lang_string('selectmoduletoviewhelp'));
        $this->add_param('course', $course->id);
    }
}
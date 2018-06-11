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

namespace theme_receptic\output;

use html_writer;

defined('MOODLE_INTERNAL') || die();

if (file_exists("$CFG->dirroot/course/format/collapsibletopics/renderer.php")) {


    require_once($CFG->dirroot . '/course/format/collapsibletopics/renderer.php');

    class format_collapsibletopics_renderer extends \format_collapsibletopics_renderer {
        /** overrides format_section_renderer_base */
        use \theme_receptic\output\format_commons;

        public function __construct(\moodle_page $page, $target) {
            parent::__construct($page, $target);

            $this->init();
            $this->iscollapsible = true;
        }

        public function print_multiple_section_page($course, $sections, $mods, $modnames, $modnamesused) {
           $this->print_multiple_sections($course, $sections, $mods, $modnames, $modnamesused, true);
        }
    }
}

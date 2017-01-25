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

defined('MOODLE_INTERNAL') || die();

$THEME->name = 'receptic';
$THEME->sheets = ['font-awesome'];
// For TinyMCE only.
$THEME->editor_sheets = [];
$THEME->parents = ['boost'];
// Parent theme Boost does not support dock functionality.
$THEME->enable_dock = false;
// Setting not recommended anymore.
$THEME->yuicssmodules = array();
// Mandatory to be able to override renderers.
$THEME->rendererfactory = 'theme_overridden_renderer_factory';
$THEME->requiredblocks = 'settings';
// Move the add block functionality to be in the flat navigation rather than the block region.
$THEME->addblockposition = BLOCK_ADDBLOCK_POSITION_CUSTOM;
// Override preset selection from boost theme to allow selecting preset from both themes.
$THEME->scss = function($theme) {
    return theme_receptic_get_main_scss_content($theme);
};
// Function that returns some SCSS as a string to prepend to the main SCSS file. (like brandprimary in boost theme). Callback is defined in lib.php file.
$THEME->prescsscallback = 'theme_receptic_get_pre_scss';
// Function that returns SCSS to append to our main SCSS for this theme.
$THEME->extrascsscallback = 'theme_receptic_get_extra_scss';

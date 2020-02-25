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
 * A login page layout for Receptic theme.
 *
 * @package    theme_receptic
 * @author     Jean-Roch Meurisse
 * @copyright  2016 - Cellule TICE - Unversite de Namur
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$bodyattributes = $OUTPUT->body_attributes();
$haslogoleft = !empty(get_config('theme_receptic', 'logoleft'));
$haslogocenter = !empty(get_config('theme_receptic', 'logocenter'));
$haslogoright = !empty(get_config('theme_receptic', 'logoright'));
$displaybrandbanner = get_config('theme_receptic', 'brandbanner');

$templatecontext = [
    'sitename' => format_string($SITE->shortname, true, ['context' => context_course::instance(SITEID), "escape" => false]),
    'output' => $OUTPUT,
    'bodyattributes' => $bodyattributes,
    'forgetpasslink' => false,
    'displaybrandbanner' => $displaybrandbanner,
    'logoleft' => $haslogoleft,
    'logocenter' => $haslogocenter,
    'logoright' => $haslogoright,
    'logininfo' => get_config('theme_receptic', 'logininfo')
];

echo $OUTPUT->render_from_template('theme_receptic/login', $templatecontext);


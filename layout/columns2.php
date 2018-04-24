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
 * A two column layout for the boost theme.
 *
 * @package   theme_boost
 * @copyright 2016 Damyon Wiese
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

user_preference_allow_ajax_update('drawer-open-nav', PARAM_ALPHA);
user_preference_allow_ajax_update('blocks-collapsed', PARAM_ALPHA);
user_preference_allow_ajax_update('sidepre-open', PARAM_ALPHA);
user_preference_allow_ajax_update('flashbox-teacher-hidden', PARAM_ALPHA);
user_preference_allow_ajax_update('flashbox-student-hidden', PARAM_ALPHA);
$context = $this->page->context;
if ($context->contextlevel == CONTEXT_COURSE) {
    user_preference_allow_ajax_update('sections-toggle-' . $this->page->course->id, PARAM_RAW);
}
require_once($CFG->libdir . '/behat/lib.php');

$sectionstogglestate = get_user_preferences('sections-toggle-' . $this->page->course->id);

if (empty($sectionstogglestate)) {
    $sectionstogglestate = '{}';
}
$params = array('course' => $this->page->course->id, 'sectionstoggle' => $sectionstogglestate);
$this->page->requires->js_call_amd('theme_receptic/ux', 'init', array($params));

$iscontextcourse = $context->contextlevel == CONTEXT_COURSE || $context->contextlevel == CONTEXT_MODULE;
$params = new stdClass();

if ($context->contextlevel == CONTEXT_SYSTEM) {
    $shownavdrawer = true;
} else if ($context->contextlevel == CONTEXT_USER || $this->page->course->id == SITEID) {
    $shownavdrawer = true;
} else {
    $shownavdrawer = true;
}
$shownavdrawer = true;
if (isloggedin()) {
    $navdraweropen = (get_user_preferences('drawer-open-nav', 'true') == 'true'
            && $shownavdrawer
            );
    $blockscollapsed = get_user_preferences('blocks-collapsed', 'false') == 'true';
    $draweropenright = (get_user_preferences('sidepre-open', 'true') == 'true');
} else {
    $navdraweropen = false;
    $blockscollapsed = true;
    $draweropenright = false;
}
$extraclasses = [];

global $USER;
if ($USER->auth == 'lti') {
    $extraclasses[] = 'ltibridge';
    $navdraweropen = false;
    $blockscollapsed = true;
}

if ($navdraweropen) {
    $extraclasses[] = 'drawer-open-left';
}

if ($blockscollapsed) {
    //$extraclasses[] = 'blocks-hidden';
}

$blockshtml = $OUTPUT->blocks('side-pre');
$hasblocks = (strpos($blockshtml, 'data-block=') !== false) || ($PAGE->user_is_editing() && strpos($this->page->pagetype, 'mod-' !== 0));

if ($draweropenright && $hasblocks) {
    $extraclasses[] = 'drawer-open-right';
}

$bodyattributes = $OUTPUT->body_attributes($extraclasses);

$regionmainsettingsmenu = $OUTPUT->region_main_settings_menu();
$googlefonts = array(
    'Open+Sans:400,400italic,700,700italic,800,800italic',
    'Roboto+Slab:400,700',
    'Roboto+Condensed',
    'Roboto:400,400italic,700,700italic,900,900italic'
);

$haslogoleft = !empty(get_config('theme_receptic', 'logoleft'));
$haslogocenter = !empty(get_config('theme_receptic', 'logocenter'));
$haslogoright = !empty(get_config('theme_receptic', 'logoright'));

$templatecontext = [
    'sitename' => format_string($SITE->shortname, true, ['context' => context_course::instance(SITEID), "escape" => false]),
    'output' => $OUTPUT,
    'sidepreblocks' => $blockshtml,
    'hasblocks' => $hasblocks,
    'bodyattributes' => $bodyattributes,
    'navdraweropen' => $navdraweropen,
    'draweropenright' => $draweropenright,
    'regionmainsettingsmenu' => $regionmainsettingsmenu,
    'hasregionmainsettingsmenu' => !empty($regionmainsettingsmenu),
    'displaybrandbanner' => true,
    'googlefonts' => $googlefonts,
    'shownavdrawer' => $shownavdrawer,
    'iscontextcourse' => $iscontextcourse,
    'shownavbar' => true,
    'logoleft' => $haslogoleft,
    'logocenter' => $haslogocenter,
    'logoright' => $haslogoright
];

$templatecontext['flatnavigation'] = $PAGE->flatnav;
echo $OUTPUT->render_from_template('theme_receptic/columns2', $templatecontext);


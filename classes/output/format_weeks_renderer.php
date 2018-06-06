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

use context_course;
use completion_info;
use html_writer;
use moodle_url;
use moodle_page;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/format/weeks/renderer.php');

class format_weeks_renderer extends \format_weeks_renderer {
    /** overrides format_section_renderer_base */
    protected $collapsedsections;

        /**
         * Constructor method, calls the parent constructor
         *
         * @param moodle_page $page
         * @param string $target one of rendering target constants
         */
    public function __construct(moodle_page $page, $target) {
        parent::__construct($page, $target);
        $course = course_get_format($page->course)->get_course();
        $this->collapsedsections = $course->coursedisplay == COURSE_DISPLAY_SINGLEPAGE
                && $course->format == 'topics'
                && get_config('theme_receptic' , 'collapsingtopics'
                );
    }

    public function print_multiple_section_page($course, $sections, $mods, $modnames, $modnamesused) {
        global $PAGE, $DB, $USER;

        /*if(!$this->collapsedsections) {
            return parent::print_multiple_section_page($course, $sections, $mods, $modnames, $modnamesused);
        }*/

        //test computing of red and orange balls on course page.
        $redballsactivated = get_config('theme_receptic', 'enableredballs');
        $orangeballsactivated = get_config('theme_receptic', 'enableorangeballs');

        if ($redballsactivated) {
            $userredballs = get_user_preferences('user_redballs');
            if (is_null($userredballs)) {
                $defaultredballslookback = get_config('theme_receptic', 'redballs_lookback');
                $starttime = time() - ($defaultredballslookback * 24 * 60 * 60);
            } else {
                $starttime = $DB->get_field('user', 'lastlogin', array('id' => $USER->id));
            }

            $newitemsforuser = array();
            if (!empty($userredballs)) {
                $newitemsforuser = explode(',', $userredballs);
            }
        }
        if ($orangeballsactivated) {
            $userorangeballs = get_user_preferences('user_orangeballs');
            if (is_null($userorangeballs)) {
                $defaultlookback = get_config('theme_receptic', 'redballs_lookback');
                $starttime = time() - ($defaultlookback * 24 * 60 * 60);
            } else {
                $starttime = $DB->get_field('user', 'lastlogin', array('id' => $USER->id));
            }

            $updateditemsforuser = array();
            if (!empty($userorangeballs)) {
                $updateditemsforuser = explode(',', $userorangeballs);
            }
        }

        if ($redballsactivated) {

            $visiblereditems = array();
            $newitemsforcourse = theme_receptic_get_redballs($course, $starttime);

            $newitemsforuser = array_merge($newitemsforuser, $newitemsforcourse);

            $newitemsforuser = array_unique($newitemsforuser);
            set_user_preference('user_redballs', implode(',', array_unique($newitemsforuser)));

            if ($orangeballsactivated) {
                $visibleorangeitems = array();
                $updateditemsforcourse = theme_receptic_get_orangeballs($course, $starttime);
                //print_object($updateditemsforcourse);
                $updateditemsforuser = array_merge($updateditemsforuser, $updateditemsforcourse);
                $updateditemsforuser = array_unique($updateditemsforuser);
            }

            $modinfo = get_fast_modinfo($course);

            foreach ($modinfo->cms as $cm) {
                if ($cm->uservisible && !$cm->is_stealth() && in_array($cm->id, $newitemsforuser)) {
                    $visiblereditems[] = $cm->id;
                }
                if ($orangeballsactivated && $cm->uservisible && !$cm->is_stealth() && in_array($cm->id,
                        $updateditemsforuser) && !in_array($cm->id, $newitemsforuser)
                ) {
                    $visibleorangeitems[] = $cm->id;
                }
            }
            if ($orangeballsactivated) set_user_preference('user_orangeballs', implode(',', array_unique($updateditemsforuser)));

        }
        // end test computing of red and orange balls on cours page.
//print_object($newitemsforuser);
        $modinfo = get_fast_modinfo($course);
        $course = course_get_format($course)->get_course();

        $context = context_course::instance($course->id);
        // Title with completion help icon.
        $completioninfo = new completion_info($course);
        echo $completioninfo->display_help_icon();
        echo $this->output->heading($this->page_title(), 2, 'accesshide');

        // Copy activity clipboard..
        echo $this->course_activity_clipboard($course, 0);

        // Now the list of sections..
        echo $this->start_section_list();
        $numsections = course_get_format($course)->get_last_section_number();

        foreach ($modinfo->get_section_info_all() as $section => $thissection) {
            if ($section == 0) {
                // 0-section is displayed a little different then the others.
                if ($thissection->summary or !empty($modinfo->sections[0]) or $PAGE->user_is_editing()) {
                    $this->page->requires->strings_for_js(array('collapseall', 'expandall'), 'moodle');
                    $modules = $this->courserenderer->theme_receptic_course_section_cm_list($course, $thissection, 0);
                    echo $this->section_header($thissection, $course, false, 0);
                    echo $modules;
                    echo $this->courserenderer->course_section_add_cm_control($course, 0, 0);
                    echo '<div class="collapsible-actions" >
    <a href="#" class="expandall" role="button">' . get_string('expandall') . '
    </a>
</div>';
                    echo $this->section_footer();
                }
                continue;
            }
            //if ($section > $course->numsections) {
            if ($section > $numsections) {
                // Activities inside this section are 'orphaned', this section will be printed as 'stealth' below.
                continue;
            }
            // Show the section if the user is permitted to access it, OR if it's not available
            // but there is some available info text which explains the reason & should display.
            $showsection = $thissection->uservisible ||
                ($thissection->visible && !$thissection->available &&
                    !empty($thissection->availableinfo));
            if (!$showsection) {
                // If the hiddensections option is set to 'show hidden sections in collapsed
                // form', then display the hidden section message - UNLESS the section is
                // hidden by the availability system, which is set to hide the reason.
                if (!$course->hiddensections && $thissection->available) {
                    echo $this->section_hidden($section, $course->id);
                }

                continue;
            }

            if (!$PAGE->user_is_editing() && $course->coursedisplay == COURSE_DISPLAY_MULTIPAGE) {
                // Display section summary only.
                $this->courserenderer->theme_receptic_course_section_cm_list($course, $thissection, 0);
                echo $this->section_header($thissection, $course, null);
                echo $this->section_summary($thissection, $course, null);
            } else {

                if ($thissection->uservisible) {
                    $modules = $this->courserenderer->theme_receptic_course_section_cm_list($course, $thissection, 0);
                    $control = $this->courserenderer->course_section_add_cm_control($course, $section, 0);
                    echo $this->section_header($thissection, $course, false, 0);
                    echo $modules;
                    echo $control;
                    echo $this->section_footer();
                }
            }
        }

        if ($PAGE->user_is_editing() and has_capability('moodle/course:update', $context)) {
            // Print stealth sections if present.
            foreach ($modinfo->get_section_info_all() as $section => $thissection) {
                if ($section <= $numsections or empty($modinfo->sections[$section])) {
                    // This is not stealth section or it is empty.
                    continue;
                }
                echo $this->stealth_section_header($section);
                echo $this->courserenderer->theme_receptic_course_section_cm_list($course, $thissection, 0);
                echo $this->stealth_section_footer();
            }

            echo $this->end_section_list();

            echo $this->change_number_sections($course, 0);
        } else {
            echo $this->end_section_list();
        }
    }

    /**
     * Generate the starting container html for a list of sections overrides format_section_renderer_base
     * @return string HTML to output.
     */
    protected function start_section_list() {
        return html_writer::start_tag('ul', array('class' => 'accordion topics', 'aria-multiselectable' => true));
    }

    /**
     * Overrides format_section_renderer_base
     * Generate the display of the header part of a section before
     * course modules are included
     *
     * @param stdClass $section The course_section entry from DB
     * @param stdClass $course The course entry from DB
     * @param bool $onsectionpage true if being printed on a single-section page
     * @param int $sectionreturn The section to return to after an action
     * @return string HTML to output.
     */
    protected function section_header($section, $course, $onsectionpage, $sectionreturn=null) {
        global $PAGE;

        $o = '';
        $currenttext = '';
        $sectionstyle = '';

        if ($section->section != 0) {
            // Only in the non-general sections.
            if (!$section->visible) {
                $sectionstyle = ' hidden';
            } else if (course_get_format($course)->is_section_current($section)) {
                $sectionstyle = ' current';
            }
        }

        $o .= html_writer::start_tag('li', array('id' => 'section-' . $section->section,
            'class' => 'section main clearfix' . $sectionstyle, 'role' => 'region',
            'aria-label' => get_section_name($course, $section)));

        // Create a span that contains the section title to be used to create the keyboard section move menu.
        $o .= html_writer::tag('span', get_section_name($course, $section), array('class' => 'hidden sectionname'));

        $leftcontent = $this->section_left_content($section, $course, $onsectionpage);
        $o .= html_writer::tag('div', $leftcontent, array('class' => 'left side'));

        $rightcontent = $this->section_right_content($section, $course, $onsectionpage);
        $o .= html_writer::tag('div', $rightcontent, array('class' => 'right side'));
        $o .= html_writer::start_tag('div', array('class' => 'content'));

        // When not on a section page, we display the section titles except the general section if null.
        $hasnamenotsecpg = (!$onsectionpage && ($section->section != 0 || !is_null($section->name)));

        // When on a section page, we only display the general section title, if title is not the default one.
        $hasnamesecpg = ($onsectionpage && ($section->section == 0 && !is_null($section->name)));

        $classes = ' accesshide';
        if ($hasnamenotsecpg || $hasnamesecpg) {
            $classes = '';
        }
        if (!$PAGE->user_is_editing()) {
            $sectionname = html_writer::tag('span', $this->section_title_without_link($section, $course),
                array('class' => 'sectionname'));
            // Jrm add collapse toggle.
            if (course_get_format($course)->is_section_current($section)) {
                $o .= '<a class="sectiontoggle" data-toggle="collapse" data-parent="accordion" href="#collapse-' .
                    $section->section .
                    '" aria-expanded="true" aria-controls="collapse-' .
                    $section->section .
                    '">&nbsp;' . $sectionname . '</a> ';
            } else if ($section->section != 0) {
                $o .= '<a class="sectiontoggle collapsed" data-toggle="collapse" data-parent="accordion" href="#collapse-' .
                    $section->section .
                    '" aria-expanded="false" aria-controls="collapse-' .
                    $section->section .
                    '">&nbsp;' . $sectionname;
                if ($section->hotcount) {
                    if ($section->hotcount > 9) {
                        $extrahotclass = ' high';
                    } else {
                        $extrahotclass = '';
                    }
                    $o .= '<span title="' . $section->hotcount . ' éléments ajoutés" class="redball-count' . $extrahotclass . '">' . $section->hotcount . '</span>';
                }
                if ($section->warmcount) {
                    if ($section->warmcount > 9) {
                        $extrawarmclass = ' high';
                    } else {
                        $extrawarmclass = '';
                    }
                    $o .= '<span title="' . $section->warmcount . ' contenus modifiés" class="orangeball-count' . $extrawarmclass . '">' . $section->warmcount . '</span>';
                }
                $o .= '</a> ';
            }
            // Jrm end collapse toggle.

            $o .= '<div class="clearfix">';
            $o .= $this->section_availability($section) . '</div>';
            $o .= $this->section_summary($section, $course, null);
            // Jrm add div around content to allow section collapsing.
        } else {
            $sectionname = html_writer::tag('span', $this->section_title_without_link($section, $course));
            if ($section->hotcount) {
                $sectionname .= '<span title="' . $section->hotcount . ' éléments ajoutés" class="redball-count">' . $section->hotcount . '</span>';
            }
            if ($section->warmcount) {
                $sectionname .= '<span title="' . $section->warmcount . ' contenus modifiés" class="orangeball-count">' . $section->warmcount . '</span>';
            }
            // Jrm add collapse toggle.
            if (course_get_format($course)->is_section_current($section)) {
                $o .= '<a class="sectiontoggle" data-toggle="collapse" data-parent="accordion" href="#collapse-' .
                    $section->section .
                    '" aria-expanded="true" aria-controls="collapse-' .
                    $section->section .
                    '">&nbsp;</a> ';
            } else if ($section->section != 0) {
                $o .= '<a class="sectiontoggle collapsed" data-toggle="collapse" data-parent="accordion" href="#collapse-' .
                    $section->section .
                    '" aria-expanded="false" aria-controls="collapse-' .
                    $section->section .
                    '">&nbsp;</a> ';
            }
            // Jrm end collapse toggle.

            $o .= '<div class="clearfix">' . $this->output->heading($sectionname, 3, 'sectionname' . $classes);
            $o .= $this->section_availability($section) . '</div>';
            $o .= $this->section_summary($section, $course, null);
            // Jrm add div around content to allow section collapsing.
        }
        if ($section->section == 0 || course_get_format($course)->is_section_current($section)) {
            $classes = "collapse show";
        } else {
            $classes = "collapse";
        }
            $o .= '<div id="collapse-' .
                $section->section .
                '" class="' .
                $classes .
                '" role="tabpanel" aria-labelledby="heading' .
                $section->section .
                '">';
        // Jrm end div.

        return $o;
    }

    /**
     * Generate the display of the footer part of a section
     *
     * @return string HTML to output.
     */
    protected function section_footer() {
        $o = html_writer::end_tag('div'); // Jrm end div surrounding content to allow section collapsing.
        $o .= html_writer::end_tag('li');

        return $o;
    }

    /**
     * Override to add spacer into current section too.
     * Generate the content to displayed on the left part of a section
     * before course modules are included
     *
     * @param stdClass $section The course_section entry from DB
     * @param stdClass $course The course entry from DB
     * @param bool $onsectionpage true if being printed on a section page
     * @return string HTML to output.
     */
    protected function section_left_content($section, $course, $onsectionpage) {
        $o = $this->output->spacer();

        if ($section->section != 0) {
            // Only in the non-general sections.
            if (course_get_format($course)->is_section_current($section)) {
                $o .= get_accesshide(get_string('currentsection', 'format_'.$course->format));
            }
        }

        return $o;
    }

    protected function section_summary($section, $course, $mods) {
        $o = '';
        $o .= html_writer::start_tag('div', array('class' => 'summarytext'));
        $o .= $this->format_summary_text($section);
        $o .= html_writer::end_tag('div');
        $o .= $this->section_activity_summary($section, $course, null);

        return $o;
    }

    public function section_availability($section) {
        $context = context_course::instance($section->course);
        $canviewhidden = has_capability('moodle/course:viewhiddensections', $context);
        return html_writer::span($this->section_availability_message($section, $canviewhidden), 'section_availability');
    }

    /**
     * If section is not visible, display the message about that ('Not available
     * until...', that sort of thing). Otherwise, returns blank.
     *
     * For users with the ability to view hidden sections, it shows the
     * information even though you can view the section and also may include
     * slightly fuller information (so that teachers can tell when sections
     * are going to be unavailable etc). This logic is the same as for
     * activities.
     *
     * @param section_info $section The course_section entry from DB
     * @param bool $canviewhidden True if user can view hidden sections
     * @return string HTML to output
     */
    protected function section_availability_message($section, $canviewhidden) {
        global $CFG;
        $o = '';
        if (!$section->visible) {
            if ($canviewhidden) {
                $o .= $this->courserenderer->availability_info(get_string('hiddenfromstudents'), 'ishidden');
            }
        } else if (!$section->uservisible) {
            if ($section->availableinfo) {
                // Note: We only get to this function if availableinfo is non-empty,
                // so there is definitely something to print.
                $formattedinfo = \core_availability\info::format_info(
                    $section->availableinfo, $section->course);
                $o .= $this->courserenderer->availability_info($formattedinfo);
            }
        } else if ($canviewhidden && !empty($CFG->enableavailability)) {
            // Check if there is an availability restriction.
            $ci = new \core_availability\info_section($section);
            $fullinfo = $ci->get_full_information();
            if ($fullinfo) {
                $formattedinfo = \core_availability\info::format_info(
                    $fullinfo, $section->course);
                $o .= $this->courserenderer->availability_info($formattedinfo);
            }
        }
        return $o;
    }
}

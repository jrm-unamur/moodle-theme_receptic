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

require_once($CFG->dirroot . "/course/format/topics/renderer.php");
//require_once($CFG->dirroot . "/theme/receptic/lib.php");

class format_topics_renderer extends \format_topics_renderer {

/* Output the html for a multiple section page
*
* @param stdClass $course The course entry from DB
* @param array $sections (argument not used)
* @param array $mods (argument not used)
* @param array $modnames (argument not used)
* @param array $modnamesused (argument not used)
*/
    public function print_multiple_section_page($course, $sections, $mods, $modnames, $modnamesused) {
        global $PAGE;

        //test computing of red and orange balls on course page.
        $ballsactivated = get_config('theme_receptic', 'enableballs');
        if ($ballsactivated) {
            list($newitemsforuser, $updateditemsforuser, $starttime) = theme_receptic_init_vars_for_hot_items_computing();
            theme_receptic_compute_redballs($course, $starttime, $newitemsforuser);
            theme_receptic_compute_orangeballs($course, $starttime, $updateditemsforuser);
        }
        // end test computing of red and orange balls on cours page.

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
                // 0-section is displayed a little different then the others
                if ($thissection->summary or !empty($modinfo->sections[0]) or $PAGE->user_is_editing()) {
                    $modules = $this->courserenderer->theme_receptic_course_section_cm_list($course, $thissection, 0);
                    echo $this->section_header($thissection, $course, false, 0);
                    echo $modules;
                    echo $this->courserenderer->course_section_add_cm_control($course, 0, 0);
                    echo $this->section_footer();
                }
                continue;
            }
            if ($section > $numsections) {
                // activities inside this section are 'orphaned', this section will be printed as 'stealth' below
                continue;
            }
            // Show the section if the user is permitted to access it, OR if it's not available
            // but there is some available info text which explains the reason & should display,
            // OR it is hidden but the course has a setting to display hidden sections as unavilable.
            $showsection = $thissection->uservisible ||
                ($thissection->visible && !$thissection->available && !empty($thissection->availableinfo)) ||
                (!$thissection->visible && !$course->hiddensections);

            if (!$showsection) {
                continue;
            }

            $modules = $this->courserenderer->theme_receptic_course_section_cm_list($course, $thissection, 0);
            if (!$PAGE->user_is_editing() && $course->coursedisplay == COURSE_DISPLAY_MULTIPAGE) {
                // Display section summary only.
                echo $this->section_summary($thissection, $course, null);
            } else {

                echo $this->section_header($thissection, $course, false, 0);
                if ($thissection->uservisible) {
                    echo $modules;
                    echo $this->courserenderer->course_section_add_cm_control($course, $section, 0);
                }
                echo $this->section_footer();
            }
        }

        if ($PAGE->user_is_editing() and has_capability('moodle/course:update', $context)) {
            // Print stealth sections if present.
            foreach ($modinfo->get_section_info_all() as $section => $thissection) {
                if ($section <= $numsections or empty($modinfo->sections[$section])) {
                    // this is not stealth section or it is empty
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
     * Output the html for a single section page .
     *
     * @param stdClass $course The course entry from DB
     * @param array $sections (argument not used)
     * @param array $mods (argument not used)
     * @param array $modnames (argument not used)
     * @param array $modnamesused (argument not used)
     * @param int $displaysection The section number in the course which is being displayed
     */
    public function print_single_section_page($course, $sections, $mods, $modnames, $modnamesused, $displaysection) {
        global $PAGE;

        $modinfo = get_fast_modinfo($course);
        $course = course_get_format($course)->get_course();

        // Can we view the section in question?
        if (!($sectioninfo = $modinfo->get_section_info($displaysection)) || !$sectioninfo->uservisible) {
            // This section doesn't exist or is not available for the user.
            // We actually already check this in course/view.php but just in case exit from this function as well.
            print_error('unknowncoursesection', 'error', course_get_url($course),
                format_string($course->fullname));
        }

        //test computing of red and orange balls on course page.
        $ballsactivated = get_config('theme_receptic', 'enableballs');
        if ($ballsactivated) {
            list($newitemsforuser, $updateditemsforuser, $starttime) = theme_receptic_init_vars_for_hot_items_computing();
            theme_receptic_compute_redballs($course, $starttime, $newitemsforuser);
            theme_receptic_compute_orangeballs($course, $starttime, $updateditemsforuser);
        }
        // end test computing of red and orange balls on cours page.

        // Copy activity clipboard..
        echo $this->course_activity_clipboard($course, $displaysection);
        $thissection = $modinfo->get_section_info(0);

        if ($thissection->summary or !empty($modinfo->sections[0]) or $PAGE->user_is_editing()) {
            $modules = $this->courserenderer->theme_receptic_course_section_cm_list($course, $thissection, $displaysection);
            echo parent::start_section_list();
            echo parent::section_header($thissection, $course, true, $displaysection);
            echo $modules;//$this->courserenderer->course_section_cm_list($course, $thissection, $displaysection);
            echo $this->courserenderer->course_section_add_cm_control($course, 0, $displaysection);
            echo $this->section_footer();
            echo parent::end_section_list();
        }

        // Start single-section div
        echo html_writer::start_tag('div', array('class' => 'single-section'));

        // The requested section page.
        $thissection = $modinfo->get_section_info($displaysection);

        // Title with section navigation links.
        $sectionnavlinks = $this->get_nav_links($course, $modinfo->get_section_info_all(), $displaysection);
        $sectiontitle = '';
        $sectiontitle .= html_writer::start_tag('div', array('class' => 'section-navigation navigationtitle'));
        $sectiontitle .= html_writer::tag('span', $sectionnavlinks['previous'], array('class' => 'mdl-left'));
        $sectiontitle .= html_writer::tag('span', $sectionnavlinks['next'], array('class' => 'mdl-right'));

        $modules = $this->courserenderer->theme_receptic_course_section_cm_list($course, $thissection, $displaysection);
        $balls = '';
        if ($ballsactivated) {

            if ($thissection->hotcount) {
                if ($thissection->hotcount > 9) {
                    $extrahotclass = ' high';
                } else {
                    $extrahotclass = '';
                }
                $balls .= '<span title="' . $thissection->hotcount . ' éléments ajoutés" class="redball-count' . $extrahotclass . '">' . $thissection->hotcount . '</span>';
            }
            if ($thissection->warmcount) {
                if ($thissection->warmcount > 9) {
                    $extrawarmclass = ' high';
                } else {
                    $extrawarmclass = '';
                }
                $balls .= '<span title="' . $thissection->warmcount . ' contenus modifiés" class="orangeball-count' . $extrawarmclass . '">' . $thissection->warmcount . '</span>';
            }
        }

        // Title attributes
        $classes = 'sectionname';
        if (!$thissection->visible) {
            $classes .= ' dimmed_text';
        }
        $sectionname = html_writer::tag('span', $this->section_title_without_link($thissection, $course));
        $sectiontitle .= $this->output->heading($sectionname . $balls, 3, $classes);

        $sectiontitle .= html_writer::end_tag('div');
        echo $sectiontitle;

        // Now the list of sections..
        echo parent::start_section_list();
        echo parent::section_header($thissection, $course, true, $displaysection);
        // Show completion help icon.
        $completioninfo = new completion_info($course);

        echo $completioninfo->display_help_icon();
        echo $modules;
        //echo $this->courserenderer->course_section_cm_list($course, $thissection, $displaysection);
        echo $this->courserenderer->course_section_add_cm_control($course, $displaysection, $displaysection);
        echo $this->section_footer();
        echo $this->end_section_list();

        // Display section bottom navigation.
        $sectionbottomnav = '';
        $sectionbottomnav .= html_writer::start_tag('div', array('class' => 'section-navigation mdl-bottom'));
        $sectionbottomnav .= html_writer::tag('span', $sectionnavlinks['previous'], array('class' => 'mdl-left'));
        $sectionbottomnav .= html_writer::tag('span', $sectionnavlinks['next'], array('class' => 'mdl-right'));
        $sectionbottomnav .= html_writer::tag('div', $this->section_nav_selection($course, $sections, $displaysection),
            array('class' => 'mdl-align'));
        $sectionbottomnav .= html_writer::end_tag('div');
        echo $sectionbottomnav;

        // Close single-section div.
        echo html_writer::end_tag('div');
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

        $sectionname = html_writer::tag('span', $this->section_title_without_link($section, $course));

        $ballsactivated = get_config('theme_receptic', 'enableballs');
        $balls = '';
        if ($ballsactivated) {

            if ($section->hotcount) {
                if ($section->hotcount > 9) {
                    $extrahotclass = ' high';
                } else {
                    $extrahotclass = '';
                }
                $balls .= '<span title="' . $section->hotcount . ' éléments ajoutés" class="redball-count' . $extrahotclass . '">' . $section->hotcount . '</span>';
            }
            if ($section->warmcount) {
                if ($section->warmcount > 9) {
                    $extrawarmclass = ' high';
                } else {
                    $extrawarmclass = '';
                }
                $balls .= '<span title="' . $section->warmcount . ' contenus modifiés" class="orangeball-count' . $extrawarmclass . '">' . $section->warmcount . '</span>';
            }
        }
        $o .= $this->output->heading($sectionname . $balls, 3, 'sectionname' . $classes);
        $o .= $this->section_availability($section);
//if($section->section == 1) print_object($this->section_availability($section));
        $o .= html_writer::start_tag('div', array('class' => 'summary'));
        if ($section->uservisible || $section->visible) {
            // Show summary if section is available or has availability restriction information.
            // Do not show summary if section is hidden but we still display it because of course setting
            // "Hidden sections are shown in collapsed form".
            $o .= $this->format_summary_text($section);
        }
        $o .= html_writer::end_tag('div');

        return $o;
    }

    /**
     * Generate a summary of a section for display on the 'coruse index page'
     *
     * @param stdClass $section The course_section entry from DB
     * @param stdClass $course The course entry from DB
     * @param array    $mods (argument not used)
     * @return string HTML to output.
     */
    protected function section_summary($section, $course, $mods) {
        $classattr = 'section main section-summary clearfix';
        $linkclasses = '';

        // If section is hidden then display grey section link
        if (!$section->visible) {
            $classattr .= ' hidden';
            $linkclasses .= ' dimmed_text';
        } else if (course_get_format($course)->is_section_current($section)) {
            $classattr .= ' current';
        }

        $ballsactivated = get_config('theme_receptic', 'enableballs');
        $balls = '';
        if ($ballsactivated) {

            if ($section->hotcount) {
                if ($section->hotcount > 9) {
                    $extrahotclass = ' high';
                } else {
                    $extrahotclass = '';
                }
                $balls .= '<span title="' . $section->hotcount . ' éléments ajoutés" class="redball-count' . $extrahotclass . '">' . $section->hotcount . '</span>';
            }
            if ($section->warmcount) {
                if ($section->warmcount > 9) {
                    $extrawarmclass = ' high';
                } else {
                    $extrawarmclass = '';
                }
                $balls .= '<span title="' . $section->warmcount . ' contenus modifiés" class="orangeball-count' . $extrawarmclass . '">' . $section->warmcount . '</span>';
            }
        }

        $title = get_section_name($course, $section);
        $o = '';
        $o .= html_writer::start_tag('li', array('id' => 'section-'.$section->section,
            'class' => $classattr, 'role'=>'region', 'aria-label'=> $title));

        $o .= html_writer::tag('div', '', array('class' => 'left side'));
        $o .= html_writer::tag('div', '', array('class' => 'right side'));
        $o .= html_writer::start_tag('div', array('class' => 'content'));

        if ($section->uservisible) {
            $title = html_writer::tag('a', $title,
                array('href' => course_get_url($course, $section->section), 'class' => $linkclasses));
        }
        $o .= $this->output->heading($title . $balls, 3, 'section-title');

        $o .= $this->section_availability($section);
        $o.= html_writer::start_tag('div', array('class' => 'summarytext'));

        if ($section->uservisible || $section->visible) {
            // Show summary if section is available or has availability restriction information.
            // Do not show summary if section is hidden but we still display it because of course setting
            // "Hidden sections are shown in collapsed form".
            $o .= $this->format_summary_text($section);
        }
        $o.= html_writer::end_tag('div');
        $o.= $this->section_activity_summary($section, $course, null);

        $o .= html_writer::end_tag('div');
        $o .= html_writer::end_tag('li');

        return $o;
    }

    /**
     * Generate the starting container html for a list of sections overrides format_section_renderer_base
     * @return string HTML to output.
     */
    protected function start_section_list() {
        /*if (!$this->collapsedsections) {
            return parent::start_section_list();
        }*/
        return html_writer::start_tag('ul', array('class' => 'accordion topics', 'aria-multiselectable' => true));
    }

    /**
     * Generate the display of the footer part of a section
     *
     * @return string HTML to output.
     */
    protected function section_footer() {

        /*if (!$this->collapsedsections) {
            return parent::section_footer();
        }*/
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
        /*if (!$this->collapsedsections) {
            return parent::section_left_content($section, $course, $onsectionpage);
        }*/
        $o = $this->output->spacer();

        if ($section->section != 0) {
            // Only in the non-general sections.
            if (course_get_format($course)->is_section_current($section)) {
                $o .= get_accesshide(get_string('currentsection', 'format_'.$course->format));
            }
        }

        return $o;
    }
}

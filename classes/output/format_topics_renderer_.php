<?php

/**
 * Created by PhpStorm.
 * User: jmeuriss
 * Date: 21/09/16
 * Time: 08:21
 */
namespace theme_receptic\output;

use context_course;
use completion_info;
use html_writer;
use moodle_url;

require_once($CFG->dirroot . "/course/format/topics/renderer.php");
class format_topics_renderer extends \format_topics_renderer
{
    /** overrides format_section_renderer_base */
    public function print_multiple_section_page($course, $sections, $mods, $modnames, $modnamesused) {
        global $PAGE;

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
     /*   echo $this->start_section_list();

        echo '<li class="section main clearfix" id="section-0">' .
            //'<div class="card-header" role="tab" id="heading0">' .
            '<a class="sectiontoggle" data-toggle="collapse" data-parent="accordion" href="#collapse-0" aria-expanded="false" aria-controls="collapse-1">&nbsp;</a> ' .
            '<h3 class="sectionname">' .
            '<a href="http://det-prototype.det.fundp.ac.be/jrmsandbox/course/view.php?id=4#section-0">Généralités</a> ' .
            '</h3>' .
           // '</div>' .
            '<div id="collapse-0" class="collapse show" role="tabpanel" aria-labelledby="heading0"> ' .
            //'<div class="card-block">' .
            '<div class="summary"><div class="no-overflow"><p></p><ul><li>jfqmfd<br></li><li>dsdfmqdf<br></li></ul><p></p></div></div>' .
            '<ul class="section img-text"><li class="activity forum modtype_forum " id="module-41"><div><div class="mod-indent-outer"><div class="mod-indent"></div><div><div class="activityinstance"><a class="" onclick="" href="http://det-prototype.det.fundp.ac.be/jrmsandbox/mod/forum/view.php?id=41"><img src="http://det-prototype.det.fundp.ac.be/jrmsandbox/theme/image.php?theme=receptic&amp;component=forum&amp;image=icon" class="iconlarge activityicon" alt=" " role="presentation"><span class="instancename">Annonces<span class="accesshide "> Forum</span></span></a></div></div></div></div></li><li class="activity resource modtype_resource " id="module-43"><div><div class="mod-indent-outer"><div class="mod-indent"></div><div><div class="activityinstance"><a class="" onclick="" href="http://det-prototype.det.fundp.ac.be/jrmsandbox/mod/resource/view.php?id=43"><img src="http://det-prototype.det.fundp.ac.be/jrmsandbox/theme/image.php?theme=receptic&amp;component=core&amp;image=f%2Fjpeg-24" class="iconlarge activityicon" alt=" " role="presentation"><span class="instancename">File<span class="accesshide "> Fichier</span></span></a></div><span class="actions"><form method="post" action="http://det-prototype.det.fundp.ac.be/jrmsandbox/course/togglecompletion.php" class="togglecompletion"><div><input type="hidden" name="id" value="43"><input type="hidden" name="sesskey" value="rBvjlQhG3M"><input type="hidden" name="modulename" value="File"><input type="hidden" name="completionstate" value="1"><input type="image" src="http://det-prototype.det.fundp.ac.be/jrmsandbox/theme/image.php?theme=receptic&amp;component=core&amp;image=i%2Fcompletion-manual-n" alt="Non terminé&nbsp;: File. Sélectionner pour marquer comme terminé." title="Marquer comme terminé&nbsp;: File" aria-live="polite"></div></form></span></div></div></div></li><li class="activity resource modtype_resource " id="module-44"><div><div class="mod-indent-outer"><div class="mod-indent"></div><div><div class="activityinstance"><a class="" onclick="" href="http://det-prototype.det.fundp.ac.be/jrmsandbox/mod/resource/view.php?id=44"><img src="http://det-prototype.det.fundp.ac.be/jrmsandbox/theme/image.php?theme=receptic&amp;component=core&amp;image=f%2Fpng-24" class="iconlarge activityicon" alt=" " role="presentation"><span class="instancename">Another file<span class="accesshide "> Fichier</span></span></a></div><span class="actions"><form method="post" action="http://det-prototype.det.fundp.ac.be/jrmsandbox/course/togglecompletion.php" class="togglecompletion"><div><input type="hidden" name="id" value="44"><input type="hidden" name="sesskey" value="rBvjlQhG3M"><input type="hidden" name="modulename" value="Another file"><input type="hidden" name="completionstate" value="1"><input type="image" src="http://det-prototype.det.fundp.ac.be/jrmsandbox/theme/image.php?theme=receptic&amp;component=core&amp;image=i%2Fcompletion-manual-n" alt="Non terminé&nbsp;: Another file. Sélectionner pour marquer comme terminé." title="Marquer comme terminé&nbsp;: Another file" aria-live="polite"></div></form></span></div></div></div></li></ul>' .
           // '</div>' .
        '</div></li>';


        echo $this->end_section_list();*/


        // Now the list of sections..
        echo $this->start_section_list();

        foreach ($modinfo->get_section_info_all() as $section => $thissection) {
            if ($section == 0) {
                // 0-section is displayed a little different then the others
                if ($thissection->summary or !empty($modinfo->sections[0]) or $PAGE->user_is_editing()) {
                    echo $this->section_header($thissection, $course, false, 0);
                    echo $this->courserenderer->course_section_cm_list($course, $thissection, 0);
                    echo $this->courserenderer->course_section_add_cm_control($course, 0, 0);
                    echo $this->section_footer();
                }
                continue;
            }
            if ($section > $course->numsections) {
                // activities inside this section are 'orphaned', this section will be printed as 'stealth' below
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
                echo $this->section_summary($thissection, $course, null);
            } else {
                echo $this->section_header($thissection, $course, false, 0);
                if ($thissection->uservisible) {
                    echo $this->courserenderer->course_section_cm_list($course, $thissection, 0);
                    //echo $this->courserenderer->course_section_add_cm_control($course, $section, 0);
                }
                echo $this->section_footer();
            }
        }

        if ($PAGE->user_is_editing() and has_capability('moodle/course:update', $context)) {
            // Print stealth sections if present.
            foreach ($modinfo->get_section_info_all() as $section => $thissection) {
                if ($section <= $course->numsections or empty($modinfo->sections[$section])) {
                    // this is not stealth section or it is empty
                    continue;
                }
                echo $this->stealth_section_header($section);
                echo $this->courserenderer->course_section_cm_list($course, $thissection, 0);
                echo $this->stealth_section_footer();
            }

            echo $this->end_section_list();

            echo html_writer::start_tag('div', array('id' => 'changenumsections', 'class' => 'mdl-right'));

            // Increase number of sections.
            //$straddsection = html_writer::span(get_string('addsection', 'theme_unamurui'));
            $straddsection = html_writer::span('addsection');
            $url = new moodle_url('/course/changenumsections.php',
                array('courseid' => $course->id,
                    'increase' => true,
                    'sesskey' => sesskey()));
            $icon = $this->output->pix_icon('t/add', $straddsection);
            echo html_writer::link($url, $icon . $straddsection, array('class' => 'increase-sections'));

            /*if ($course->numsections > 0) {
                // Reduce number of sections sections.
                $strremovesection = get_string('reducesections', 'moodle');
                $url = new moodle_url('/course/changenumsections.php',
                    array('courseid' => $course->id,
                        'increase' => false,
                        'sesskey' => sesskey()));
                $icon = $this->output->pix_icon('t/switch_minus', $strremovesection);
                echo html_writer::link($url, $icon.get_accesshide($strremovesection), array('class' => 'reduce-sections'));
            }*/

            echo html_writer::end_tag('div');
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

        $o.= html_writer::start_tag('li', array('id' => 'section-'.$section->section,
            'class' => 'section main clearfix'.$sectionstyle, 'role'=>'region',
            'aria-label'=> get_section_name($course, $section)));

        // Create a span that contains the section title to be used to create the keyboard section move menu.
        $o .= html_writer::tag('span', get_section_name($course, $section), array('class' => 'hidden sectionname'));

        $leftcontent = $this->section_left_content($section, $course, $onsectionpage);
        $o.= html_writer::tag('div', $leftcontent, array('class' => 'left side'));

        $rightcontent = $this->section_right_content($section, $course, $onsectionpage);
        $o.= html_writer::tag('div', $rightcontent, array('class' => 'right side'));
        $o.= html_writer::start_tag('div', array('class' => 'content'));

        // When not on a section page, we display the section titles except the general section if null
        $hasnamenotsecpg = (!$onsectionpage && ($section->section != 0 || !is_null($section->name)));

        // When on a section page, we only display the general section title, if title is not the default one
        $hasnamesecpg = ($onsectionpage && ($section->section == 0 && !is_null($section->name)));

        $classes = ' accesshide';
        if ($hasnamenotsecpg || $hasnamesecpg) {
            $classes = '';
        }
        $sectionname = html_writer::tag('span', $this->section_title($section, $course));
        //jrm add collapse toggle
        $o.= '<a class="sectiontoggle" data-toggle="collapse" data-parent="accordion" href="#collapse-' . $section->section . '" aria-expanded="false" aria-controls="collapse-1">&nbsp;</a> ';
        //jrm end collapse toggle

        $o.= $this->output->heading($sectionname, 3, 'sectionname' . $classes);

        //jrm add div around content to allow section collapsing
        $o.= '<div id="collapse-' . $section->section . '" class="collapse show" role="tabpanel" aria-labelledby="heading' . $section->section . '">';
        $o.= html_writer::start_tag('div', array('class' => 'summary'));
        $o.= $this->format_summary_text($section);
        $o.= html_writer::end_tag('div');

        $context = context_course::instance($course->id);
        $o .= $this->section_availability_message($section,
            has_capability('moodle/course:viewhiddensections', $context));

        return $o;
    }

    /**
     * Generate the display of the footer part of a section
     *
     * @return string HTML to output.
     */
    protected function section_footer() {
        $o = html_writer::end_tag('div'); //jrm end div surrounding content to allow section collapsing
        $o.= html_writer::end_tag('div');
        $o.= html_writer::end_tag('li');

        return $o;
    }


}
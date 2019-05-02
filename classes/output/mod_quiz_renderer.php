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
 * Overridden mod_quiz_renderer class.
 *
 * @package    theme_receptic
 * @author     2016 Jean-Roch Meurisse
 * @copyright  2016 University of Namur - Cellule TICE
 *
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace theme_receptic\output;

use mod_quiz_view_object;

defined('MOODLE_INTERNAL') || die;

/**
 * Overridden mod_quiz_renderer class definition.
 *
 * @package    theme_receptic
 * @author     2016 Jean-Roch Meurisse
 * @copyright  2016 University of Namur - Cellule TICE
 */
class mod_quiz_renderer extends \mod_quiz_renderer  {
    /**
     * Override to separate message output from edit button output
     * Generate a message saying that this quiz has no questions, with a button to
     * go to the edit page, if the user has the right capability.
     * @param bool $canedit whether the current user has edit rights.
     * @param moodle_url $editurl url object to edit page.
     * @return string HTML to output.
     */
    public function no_questions_message($canedit, $editurl) {
        $output = '';
        $output .= $this->notification(get_string('noquestions', 'quiz'));

        return $output;
    }

    /**
     * Override to always display edit button to privileged users
     * Work out, and render, whatever buttons, and surrounding info, should appear
     * at the end of the review page.
     * @param mod_quiz_view_object $viewobj the information required to display
     * the view page.
     * @return string HTML to output.
     */
    public function view_page_buttons(mod_quiz_view_object $viewobj) {
        $output = '';

        if (!$viewobj->quizhasquestions) {
            $output .= $this->no_questions_message($viewobj->canedit, $viewobj->editurl);
        }

        $output .= $this->access_messages($viewobj->preventmessages);
        if ($viewobj->canedit) {
            $output .= $this->single_button($viewobj->editurl, get_string('editquiz', 'quiz'), 'get');
        }
        if ($viewobj->buttontext) {
            $output .= $this->start_attempt_button($viewobj->buttontext,
                $viewobj->startattempturl, $viewobj->preflightcheckform,
                $viewobj->popuprequired, $viewobj->popupoptions);
        }

        if ($viewobj->showbacktocourse) {
            $output .= $this->single_button($viewobj->backtocourseurl,
                get_string('backtocourse', 'quiz'), 'get',
                array('class' => 'singlebutton'));
        }

        return $output;
    }
}
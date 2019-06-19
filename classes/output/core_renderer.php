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
 * Overridden core_renderer.
 *
 * @package    theme_receptic
 * @author     Jean-Roch Meurisse
 * @copyright  2016 - Cellule TICE - Unversite de Namur
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_receptic\output;

use html_writer;
use stdClass;
use moodle_url;
use custom_menu_item;
use custom_menu;
use context_system;
use context_course;
use renderer_base;
use external_settings;
use action_menu;
use action_link;
use navigation_node;
use pix_icon;
use core_plugin_manager;
use moodle_page;
use help_icon;

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . '/cohort/lib.php');

/**
 * Receptic theme core_renderer overrides.
 *
 * @package    theme_receptic
 * @author     Jean-Roch Meurisse
 * @copyright  2018 - Cellule TICE - Unversite de Namur
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_renderer extends \theme_boost\output\core_renderer {

    // Methods for editmode button in bar.
    /**
     * Method to add a permanent edit mode switch in navbar.
     *
     * @return bool|string
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    public function custom_menu_editing() {
        $html = '';
        if (!empty($this->page->theme->settings->editbutton)) {
            if ($this->page->user_allowed_editing()) {
                // Only set to false when cannot determine what the URL / params should be for a page type.
                $buttontoadd = true;
                $pagetype = $this->page->pagetype;
                $ismodeditview = strpos($pagetype, 'mod') !== false
                        && ((strpos($pagetype, 'edit') !== false)
                            || (strpos($pagetype, 'view') !== false)
                            || strpos($pagetype, 'mod') !== false);
                if (strpos($pagetype, 'admin-setting') !== false) {
                    $pagetype = 'admin-setting'; // Deal with all setting page types.
                } else if ($ismodeditview) {
                    $pagetype = 'mod-edit-view'; // Deal with all mod edit / view page types.
                } else if (strpos($pagetype, 'mod-data-field') !== false) {
                    $pagetype = 'mod-data-field'; // Deal with all mod data field page types.
                } else if (strpos($pagetype, 'mod-lesson') !== false) {
                    $pagetype = 'mod-lesson'; // Deal with all mod lesson page types.
                }
                if (strpos($this->page->bodyclasses, 'path-user')
                        || strpos($this->page->bodyclasses, 'path-grade')) {
                    $buttontoadd = false;
                }
                if ($buttontoadd) {
                    switch ($pagetype) {
                        case 'site-index':
                            $url = new moodle_url('/course/view.php');
                            $url->param('id', 1);
                            if ($this->page->user_is_editing()) {
                                $url->param('edit', 'off');
                            } else {
                                $url->param('edit', 'on');
                            }
                            $buttontoadd = true;
                            break;
                        case 'calendar-view':// Slightly faulty as even the navigation link goes back to the frontpage.  TODO: MDL.
                            $url = new moodle_url('/course/view.php');
                            $url->param('id', 1);
                            if ($this->page->user_is_editing()) {
                                $url->param('edit', 'off');
                            } else {
                                $url->param('edit', 'on');
                            }
                            $buttontoadd = false;
                            break;
                        case 'admin-index':
                        case 'admin-setting':
                            $url = $this->page->url;
                            if ($this->page->user_is_editing()) {
                                $url->param('adminedit', 0);
                            } else {
                                $url->param('adminedit', 1);
                            }
                            break;
                        case 'badges-view':
                        case 'course-admin':
                        case 'course-index':
                        case 'course-index-category':
                        case 'course-management':
                        case 'course-search':
                        case 'mod-resource-mod':
                        case 'tag-search':
                            $buttontoadd = false;
                            break;
                        case 'mod-data-field':
                        case 'mod-edit-view':
                        case 'mod-forum-discuss':
                        case 'mod-forum-index':
                        case 'mod-forum-search':
                        case 'mod-forum-subscribers':
                        case 'mod-lesson':
                        case 'mod-quiz-index':
                        case 'mod-scorm-player':
                            $url = new moodle_url('/course/view.php');
                            $url->param('id', $this->page->course->id);
                            $url->param('return', $this->page->url->out_as_local_url(false));
                            if ($this->page->user_is_editing()) {
                                $url->param('edit', 'off');
                            } else {
                                $url->param('edit', 'on');
                            }
                            break;
                        case 'my-index':
                        case 'user-profile':
                            // TODO: Not sure how to get 'id' param and if it is really needed.
                            $url = $this->page->url;
                            // Umm! Both /user/profile.php and /user/profilesys.php
                            // have the same page type but different parameters!
                            if ($this->page->user_is_editing()) {
                                $url->param('adminedit', 0);
                                $url->param('edit', 0);
                            } else {
                                $url->param('adminedit', 1);
                                $url->param('edit', 1);
                            }

                            break;
                        default:
                            $url = $this->page->url;
                            if ($this->page->user_is_editing()) {
                                $url->param('edit', 'off');
                            } else {
                                $url->param('edit', 'on');
                            }
                            break;
                    }
                }
                if ($buttontoadd) {
                    $url->param('sesskey', sesskey());
                    if (!empty($this->page->theme->settings->hidedefaulteditingbutton) && 'my-index' !== $pagetype) {
                        if (isset($this->page->cm->modname) && $this->page->cm->modname !== 'forum'
                            && $this->page->cm->modname !== 'wiki'
                            && $this->page->cm->modname !== 'quiz') {
                            // Unset button on page.
                            $this->page->set_button('');
                        }
                    } else if ('my-index' === $pagetype && !$this->page->user_is_editing()) {
                        $this->page->set_button('');
                    }
                    $templatecontext = array(
                        'url' => $url
                    );
                    $html = $this->render_from_template('theme_receptic/editmode_switch', $templatecontext);
                }
            }
        }
        return $html;
    }

    /**
     * Take a node in the nav tree and make an action menu out of it.
     * The links are injected in the action menu.
     * Override to remove turneditingonoff button from course settings menu if necessary.
     *
     * @param action_menu $menu
     * @param navigation_node $node
     * @param boolean $indent
     * @param boolean $onlytopleafnodes
     * @return boolean nodesskipped - True if nodes were skipped in building the menu
     */
    protected function build_action_menu_from_navigation(action_menu $menu,
                                                       navigation_node $node,
                                                       $indent = false,
                                                       $onlytopleafnodes = false) {
        $skipped = false;
        // Build an action menu based on the visible nodes from this navigation tree.
        foreach ($node->children as $menuitem) {
            // Skip non-useful menu items in course settings menu.
            if ($menuitem->key == 'turneditingonoff') {
                if (!empty($this->page->theme->settings->hidedefaulteditingbutton)
                    && !empty($this->page->theme->settings->editbutton)) {
                    continue;
                }
            };

            if ($menuitem->display) {
                if ($onlytopleafnodes && $menuitem->children->count()) {
                    $skipped = true;
                    continue;
                }
                if ($menuitem->action) {
                    if ($menuitem->action instanceof action_link) {
                        $link = $menuitem->action;
                        // Give preference to setting icon over action icon.
                        if (!empty($menuitem->icon)) {
                            $link->icon = $menuitem->icon;
                        }
                    } else {
                        $link = new action_link($menuitem->action, $menuitem->text, null, null, $menuitem->icon);
                    }
                } else {
                    if ($onlytopleafnodes) {
                        $skipped = true;
                        continue;
                    }
                    $link = new action_link(new moodle_url('#'), $menuitem->text, null, ['disabled' => true], $menuitem->icon);
                }
                if ($indent) {
                    $link->add_class('m-l-1');
                }
                if (!empty($menuitem->classes)) {
                    $link->add_class(implode(" ", $menuitem->classes));
                }

                $menu->add_secondary_action($link);
                $skipped = $skipped || $this->build_action_menu_from_navigation($menu, $menuitem, true);
            }
        }
        return $skipped;
    }

    /**
     * Override to place "add-a-block" button over button column.
     *
     * @param string $region
     * @param array $classes
     * @param string $tag
     * @return string
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    public function blocks($region, $classes = array(), $tag = 'aside') {
        $displayregion = $this->page->apply_theme_region_manipulations($region);
        $classes = (array)$classes;
        $classes[] = 'block-region';
        $attributes = array(
            'id' => 'block-region-'.preg_replace('#[^a-zA-Z0-9_\-]+#', '-', $displayregion),
            'class' => join(' ', $classes),
            'data-blockregion' => $displayregion,
            'data-droptarget' => '1'
        );
        if ($this->page->blocks->region_has_content($displayregion, $this)) {
            $content = $this->blocks_for_region($displayregion);
        } else {
            $content = '';
        }
        // Add-a-block button over blocks column in editing mode.
        if ($region == 'side-pre' && isset($this->page->theme->addblockposition) &&
            $this->page->theme->addblockposition == BLOCK_ADDBLOCK_POSITION_CUSTOM &&
            $this->page->user_is_editing() && $this->page->user_can_edit_blocks() &&
            ($addable = $this->page->blocks->get_addable_blocks())) {
            $url = new moodle_url($this->page->url, ['bui_addblock' => '', 'sesskey' => sesskey()]);
            $templatecontext = array(
                'url' => $url->out(),
            );
            $addblock = $this->render_from_template('theme_receptic/add_block_button', $templatecontext);
            $content = $addblock . $content;
            $blocks = [];
            foreach ($addable as $block) {
                $blocks[] = $block->name;
            }
            $params = array('blocks' => $blocks, 'url' => '?' . $url->get_query_string(false));
            $this->page->requires->js_call_amd('core/addblockmodal', 'init', array($params));
        }
        return html_writer::tag($tag, $content, $attributes);
    }

    /**
     * Override to display course settings menu in module context too.
     *
     * This is an optional menu that can be added to a layout by a theme. It contains the
     * menu for the course administration, only on the course main page.
     *
     * @return string
     */
    public function context_header_settings_menu() {
        $context = $this->page->context;
        $menu = new action_menu();

        $items = $this->page->navbar->get_items();
        $currentnode = end($items);

        $showcoursemenu = false;
        $showfrontpagemenu = false;
        $showusermenu = false;

        // We are on the course home page.
        if (($context->contextlevel == CONTEXT_COURSE || $context->contextlevel == CONTEXT_MODULE) &&
            !empty($currentnode) /*&&
            ($currentnode->type == navigation_node::TYPE_COURSE || $currentnode->type == navigation_node::TYPE_SECTION)*/) {
            $showcoursemenu = true;
        }

        $courseformat = course_get_format($this->page->course);
        // This is a single activity course format, always show the course menu on the activity main page.
        if ($context->contextlevel == CONTEXT_MODULE &&
            !$courseformat->has_view_page()) {

            $this->page->navigation->initialise();
            $activenode = $this->page->navigation->find_active_node();
            // If the settings menu has been forced then show the menu.
            if ($this->page->is_settings_menu_forced()) {
                $showcoursemenu = true;
            } else if (!empty($activenode) && ($activenode->type == navigation_node::TYPE_ACTIVITY ||
                    $activenode->type == navigation_node::TYPE_RESOURCE)) {

                // We only want to show the menu on the first page of the activity. This means
                // the breadcrumb has no additional nodes.
                if ($currentnode && ($currentnode->key == $activenode->key && $currentnode->type == $activenode->type)) {
                    $showcoursemenu = true;
                }
            }
        }

        // This is the site front page.
        if ($context->contextlevel == CONTEXT_COURSE &&
            !empty($currentnode) &&
            $currentnode->key === 'home') {
            $showfrontpagemenu = true;
        }

        // This is the user profile page.
        if ($context->contextlevel == CONTEXT_USER &&
            !empty($currentnode) &&
            ($currentnode->key === 'myprofile')) {
            $showusermenu = true;
        }

        if ($showfrontpagemenu) {
            $settingsnode = $this->page->settingsnav->find('frontpage', navigation_node::TYPE_SETTING);
            if ($settingsnode) {
                // Build an action menu based on the visible nodes from this navigation tree.
                $skipped = $this->build_action_menu_from_navigation($menu, $settingsnode, false, true);

                // We only add a list to the full settings menu if we didn't include every node in the short menu.
                if ($skipped) {
                    $text = get_string('morenavigationlinks');
                    $url = new moodle_url('/course/admin.php', array('courseid' => $this->page->course->id));
                    $link = new action_link($url, $text, null, null, new pix_icon('t/edit', ''));
                    $menu->add_secondary_action($link);
                }
            }
        } else if ($showcoursemenu) {
            $settingsnode = $this->page->settingsnav->find('courseadmin', navigation_node::TYPE_COURSE);
            if ($settingsnode) {
                // Build an action menu based on the visible nodes from this navigation tree.
                $skipped = $this->build_action_menu_from_navigation($menu, $settingsnode, false, true);

                // We only add a list to the full settings menu if we didn't include every node in the short menu.
                if ($skipped) {
                    $text = get_string('morenavigationlinks');
                    $url = new moodle_url('/course/admin.php', array('courseid' => $this->page->course->id));
                    $link = new action_link($url, $text, null, null, new pix_icon('t/edit', ''));
                    $menu->add_secondary_action($link);
                }
            }
        } else if ($showusermenu) {
            // Get the course admin node from the settings navigation.
            $settingsnode = $this->page->settingsnav->find('useraccount', navigation_node::TYPE_CONTAINER);
            if ($settingsnode) {
                // Build an action menu based on the visible nodes from this navigation tree.
                $this->build_action_menu_from_navigation($menu, $settingsnode);
            }
        }
        return $this->render($menu);
    }

    /**
     * Returns the banner left logo URL, if any.
     *
     * @return moodle_url|false
     */
    public function get_left_logo_url() {
        global $PAGE;
        return $PAGE->theme->setting_file_url('logoleft', 'logoleft');
    }

    /**
     * Returns the banner center logo URL, if any.
     *
     * @return moodle_url|false
     */
    public function get_center_logo_url() {
        global $PAGE;
        return $PAGE->theme->setting_file_url('logocenter', 'logocenter');
    }

    /**
     * Returns the banner right logo URL, if any.
     *
     * @return moodle_url|false
     */
    public function get_right_logo_url() {
        global $PAGE;
        return $PAGE->theme->setting_file_url('logoright', 'logoright');
    }

    /**
     * Renderer method for flasboxes.
     *
     * @param string $flashbox the name of the flashbox to render
     * @return bool|string
     */
    public function flashbox($flashbox) {
        global $PAGE, $USER;
        $isdismissable = get_config('theme_receptic', $flashbox . 'dismissable');
        if (($PAGE->pagetype !== 'my-index' && $PAGE->pagetype !== 'login-index')
            || (get_user_preferences($flashbox . '-hidden', false, $USER->id) === 'true')
                && $isdismissable) {
            return '';
        }
        $usercanview = false;
        if (get_config('theme_receptic', $flashbox . 'forall')) {
            $usercanview = true;
        } else {
            $targetcohorts = explode(',', get_config('theme_receptic', $flashbox . 'cohorts'));
            foreach ($targetcohorts as $cohort) {
                if (cohort_is_member($cohort, $USER->id)) {
                    $usercanview = true;
                }
            }
        }

        if (!$usercanview) {
            return '';
        }
        $message = $PAGE->theme->settings->$flashbox;
        if (empty(trim(strip_tags(str_replace('&nbsp;', '', $message))))) {
            return '';
        }
        $flashboxtype = $PAGE->theme->settings->{$flashbox . 'type'};

        switch ($flashboxtype) {
            case 'info' :
                $flashboxicon = 'lightbulb-o';
                break;
            case 'trick' :
                $flashboxicon = 'magic';
                break;
            case 'warning' :
                $flashboxicon = 'exclamation-triangle';
                break;
        }
        $data = [
            'message' => $message,
            'type' => $flashboxtype,
            'icon' => $flashboxicon,
            'hideclass' => 'hide' . $flashbox,
            'isdismissable' => $isdismissable
        ];
        return parent::render_from_template('theme_receptic/flashbox', $data);
    }

    /**
     * Calls generic method flashbox to render flashbox1.
     *
     * @return bool|string
     */
    public function flashbox1() {
        return $this->flashbox('flashbox1');
    }

    /**
     * Calls generic method flashbox to render flashbox2.
     *
     * @return bool|string
     */
    public function flashbox2() {
        return $this->flashbox('flashbox2');
    }

    /**
     * Renders warnings in course header when course is hidden and when current user has switched to another role.
     *
     * @return bool|string
     */
    public function coursewarnings() {
        global $PAGE, $COURSE, $USER;
        $hiddencoursewarning = get_config('theme_receptic', 'hiddencoursewarning');
        $switchedrolewarning = get_config('theme_receptic', 'switchedrolewarning');
        $coursewarnings = ($hiddencoursewarning || $switchedrolewarning)
            && $PAGE->context->contextlevel == CONTEXT_COURSE
            && $COURSE->id != 1;

        if ($coursewarnings) {
            $data = [
                'coursehidden' => $hiddencoursewarning && !$COURSE->visible,
                'courseid' => $COURSE->id,
            ];
            if (isloggedin() && $switchedrolewarning) {
                $opts = \user_get_user_navigation_info($USER, $this->page);
                // Role is switched.
                if (!empty($opts->metadata['asotherrole'])) {
                    // Get the role name switched to.
                    $role = $opts->metadata['rolename'];
                    // Get the URL to switch back (normal role).
                    $url = new moodle_url('/course/switchrole.php',
                        array('id'        => $COURSE->id, 'sesskey' => sesskey(), 'switchrole' => 0,
                            'returnurl' => $this->page->url->out_as_local_url(false)));
                    $data = array_merge($data, ['role' => $role, 'switchbackurl' => $url]);

                }
            }
            return parent::render_from_template('theme_receptic/coursewarnings', $data);
        }
        return '';
    }

    /**
     * Renders contact information in footer.
     *
     * @return bool|string
     */
    public function contact_info() {
        $contactemail = get_config('theme_receptic', 'contactemail');
        $contactphone = get_config('theme_receptic', 'contactphone');
        if (empty($contactemail) && empty($contactphone)) {
            return '';
        }
        $contactboth = !empty($contactemail) && !empty($contactphone);
        $data = [
            'contact_header' => get_config('theme_receptic', 'contactheader'),
            'contact_email' => get_config('theme_receptic', 'contactemail'),
            'contact_phone' => get_config('theme_receptic', 'contactphone'),
            'contact_both' => $contactboth
        ];
        return parent::render_from_template('theme_receptic/contact_info', $data);
    }

    /**
     * Renders moodle logo and "powered by statement" in footer.
     *
     * @return bool|string
     */
    public function moodle_credits() {
        if (get_config('theme_receptic', 'moodlecredits')) {
            return parent::render_from_template('theme_receptic/moodle_credits', array());
        }
        return '';
    }

    /**
     * Help icon and help message rendering.
     *
     * @param help_icon $helpicon A help icon instance
     * @return string HTML fragment
     */
    protected function render_help_icon(help_icon $helpicon) {
        $context = $helpicon->export_for_template($this);
        // ID needed for modal dialog.
        $context->linkid = $helpicon->identifier;
        // Fill body variable needed for modal mustache with text value.
        $context->body = $context->text;
        $context->helpmodal = get_config('theme_receptic', 'helptextinmodal');
        return $this->render_from_template('core/help_icon', $context);
    }
}

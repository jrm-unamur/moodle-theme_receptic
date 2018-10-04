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

defined('MOODLE_INTERNAL') || die;

class core_renderer extends \theme_boost\output\core_renderer {

    // Methods for editmode button in bar.
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

    // Override to place "add-a-block" button over button column.
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
                    $link = new action_link($url, $text, null, null, new pix_icon('t/edit', $text));
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
                    $link = new action_link($url, $text, null, null, new pix_icon('t/edit', $text));
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
     * Return the banner left logo URL, if any.
     *
     * @param int $maxwidth The maximum width, or null when the maximum width does not matter.
     * @param int $maxheight The maximum height, or null when the maximum height does not matter.
     * @return moodle_url|false
     */
    public function get_left_logo_url() {
        global $PAGE;
        return $PAGE->theme->setting_file_url('logoleft', 'logoleft');
    }

    /**
     * Return the banner left logo URL, if any.
     *
     * @param int $maxwidth The maximum width, or null when the maximum width does not matter.
     * @param int $maxheight The maximum height, or null when the maximum height does not matter.
     * @return moodle_url|false
     */
    public function get_center_logo_url() {
        global $PAGE;
        return $PAGE->theme->setting_file_url('logocenter', 'logocenter');
    }

    /**
     * Return the site's compact logo URL, if any.
     *
     * @param int $maxwidth The maximum width, or null when the maximum width does not matter.
     * @param int $maxheight The maximum height, or null when the maximum height does not matter.
     * @return moodle_url|false
     */
    public function get_right_logo_url() {
        global $PAGE;
        return $PAGE->theme->setting_file_url('logoright', 'logoright');
    }

    public function flashbox($targetaudience) {
        global $PAGE;
        $flashboxaudience = $PAGE->theme->settings->$targetaudience;
        
        if (empty(trim(strip_tags(str_replace('&nbsp;', '', $flashboxaudience))))) {
            return '';
        }
        
        $flashboxtype = $PAGE->theme->settings->{$targetaudience . 'type'};

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
            'message' => $flashboxaudience,
            'type' => $flashboxtype,
            'icon' => $flashboxicon,
            'hideclass' => 'hide' . $targetaudience
        ];
        return parent::render_from_template('theme_receptic/flashbox', $data);
    }

    public function flashboxteachers() {
        global $PAGE, $USER;

        $usercanview = user_has_role_assignment($USER->id, 1)
                    || user_has_role_assignment($USER->id, 2)
                    || user_has_role_assignment($USER->id, 3)
                    || user_has_role_assignment($USER->id, 4)
                    || is_siteadmin();
        if ($PAGE->pagetype !== 'my-index'
                    || get_user_preferences('flashbox-teacher-hidden', false, $USER->id) === 'true'
                    || !$usercanview) {
            return '';
        }
        return $this->flashbox('flashboxteachers');
    }

    public function flashboxstudents() {
        global $PAGE, $USER;

        $usercanview = user_has_role_assignment($USER->id, 5)
            || user_has_role_assignment($USER->id, 1, context_system::instance()->id)
            || is_siteadmin();
        if ($PAGE->pagetype !== 'my-index'
            || get_user_preferences('flashbox-student-hidden', false, $USER->id) === 'true'
            || !$usercanview) {
            return '';
        }
        return $this->flashbox('flashboxstudents');
    }

    public function contact_info() {
        return '<div class="contactinfo text-center">' .
        '<p>Contacter l\'équipe WebCampus:<br/>' .
        '<a href="mailto:webcampus-migration@unamur.be"> <i class="fa fa-envelope"></i> </a>' .
        ' ou <i class="fa fa-phone"></i> 081/72 50 75</p></div>';
    }

    public function moodle_credits() {

        return '<div class="moodlecredits text-center">Utilise ' .
        '<a title="Moodle" href="http://moodle.org/" target"_blank">' .
        $this->pix_icon('moodlelogo', 'moodle', 'moodle', array('class' => 'moodlelogofooter')) .
        '</a></div>';
    }
}

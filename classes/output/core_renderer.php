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
use renderer_base;
use external_settings;
use action_menu;
use action_link;
use navigation_node;
use pix_icon;

defined('MOODLE_INTERNAL') || die;

class core_renderer extends \theme_boost\output\core_renderer {

    public function extra_navbar_buttons() {
        global $OUTPUT, $CFG;
        $templatecontext = new stdClass();

        $settings = $this->page->theme->settings;

        if (!during_initial_install()) {


            $templatecontext->editbutton = $this->custom_menu_editing();
            $templatecontext->isloggedin = isloggedin();
            /*$callnineoneone = core_plugin_manager::instance()->get_plugin_info('local_callnineoneone');
            global $COURSE, $USER;
            if($callnineoneone &&
                (has_capability('local/callnineoneone:call', context_system::instance()) || user_has_role_assignment($USER->id, 3))){

                $url = new moodle_url('/local/callnineoneone/view.php', array(
                    'bodyid' => $this->page->bodyid,
                    'pagetype' => $this->page->pagetype,
                    'pagelayout' => $this->page->pagelayout,
                    'url' => $this->page->url,
                    'courseid' => $COURSE->id
                ));
                //$url = new moodle_url('/local/callnineoneone/view.php',
                //  array());
                $html = '<li title="' . get_string('callnineoneone', 'local_callnineoneone') . '"><a href="' . $url . '">' . '<i style="font-size:larger;" class="fa fa-ambulance"></i></a></li>';
                $templatecontext->callnineoneone = $html;
            } else {
                $templatecontext->callnineoneone = '';
            }*/
        }

        return $this->render_from_template('theme_receptic/extra-navbar-buttons', $templatecontext);

    }

    // Methods for editmode button in bar.
    public function custom_menu_editing() {
        $html = '';
        //if (!empty($this->page->theme->settings->editbutton)) {
        if(true){
            if ($this->page->user_allowed_editing()) {
                $buttontoadd = true; // Only set to false when cannot determine what the URL / params should be for a page type.
                $pagetype = $this->page->pagetype;
                if (strpos($pagetype, 'admin-setting') !== false) {
                    $pagetype = 'admin-setting'; // Deal with all setting page types.
                } else if ((strpos($pagetype, 'mod') !== false) &&
                    ((strpos($pagetype, 'edit') !== false) || (strpos($pagetype, 'view') !== false) || strpos($pagetype, 'mod') !== false)) {
                    $pagetype = 'mod-edit-view'; // Deal with all mod edit / view page types.
                } else if (strpos($pagetype, 'mod-data-field') !== false) {
                    $pagetype = 'mod-data-field'; // Deal with all mod data field page types.
                } else if (strpos($pagetype, 'mod-lesson') !== false) {
                    $pagetype = 'mod-lesson'; // Deal with all mod lesson page types.
                }
                switch ($pagetype) {
                    case 'site-index':
                    case 'calendar-view':  // Slightly faulty as even the navigation link goes back to the frontpage.  TODO: MDL.
                        $url = new moodle_url('/course/view.php');
                        $url->param('id', 1);
                        if ($this->page->user_is_editing()) {
                            $url->param('edit', 'off');
                        } else {
                            $url->param('edit', 'on');
                        }
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
                    case 'course-index':
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
                        // Umm! Both /user/profile.php and /user/profilesys.php have the same page type but different parameters!
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
                    /*default:
                        //$url = $this->page->url;
                        $course = $this->page->course;
                        if ($this->page->url->compare(new moodle_url('/course/view.php'), URL_MATCH_BASE)) {
                            // We are on the course page, retain the current page params e.g. section.
                            $url = clone($this->page->url);
                            //$baseurl->param('sesskey', sesskey());
                        } else {
                            // Edit on the main course page.
                            $url = new moodle_url('/course/view.php', array('id'=>$course->id, 'return'=>$this->page->url->out_as_local_url(false)));
                        }
                        if ($this->page->user_is_editing()) {
                            $url->param('edit', 'off');
                        } else {
                            $url->param('edit', 'on');
                        }
                        break;*/
                }
                if ($buttontoadd) {
                    $url->param('sesskey', sesskey());
                    if ($this->page->user_is_editing()) {
                        $editstring = get_string('turneditingoff');
                        //$editstring = 'turneditingoff';
                    } else {
                        $editstring = get_string('turneditingon');
                        //$editstring = 'turneditingon';
                    }
                    $html = '<a href="' .
                        $url .
                        '"  class="switch largescreen" title="' .
                        $editstring .
                        '">' .
                        //get_string('editmode', 'theme_receptic') .
                        ' </a>';
                    if (!empty($this->page->theme->settings->hidedefaulteditingbutton) && 'my-index' !== $pagetype) {
                        // Unset button on page.
                        $this->page->set_button('');
                    } else if ('my-index' === $pagetype && !$this->page->user_is_editing()) {
                        $this->page->set_button('');
                    }

                }
            }
        }
        return $html;
    }

    /*public function mainnavbar_extra_components() {
        if (!empty($this->page->theme->settings->navbarhomelink)) {
            $branchtitle = get_string('home');
            $branchlabel = '<i class="fa fa-home"></i>' . $branchtitle;
            $branchurl = new moodle_url('/?redirect=0');
            //$menu->add($branchlabel, $branchurl, $branchtitle);
        }

    }*/

    /*
     * This renders the bootstrap top menu.
     *
     * This renderer is needed to enable the Bootstrap style navigation.
     */
    protected function render_custom_menu(custom_menu $menu) {
        global $CFG, $USER;

        require_once($CFG->libdir . '/externallib.php');

        if (isloggedin() && !isguestuser()) {

            if (!empty($this->page->theme->settings->navbarhomelink)) {
                $branchtitle = get_string('home');
                $branchlabel = '<i class="fa fa-home"></i>' . $branchtitle;
                $branchurl = new moodle_url('/?redirect=0');
                $menu->add($branchlabel, $branchurl, $branchtitle);
            }
            if (!empty($this->page->theme->settings->navbardashboardlink)) {
                if (!empty($this->page->theme->settings->navabarhomelink)) {
                    $branchtitle = get_string('myhome');
                    $branchlabel = '<i class="fa fa-dashboard"></i> ' . $branchtitle;
                    $branchurl = new moodle_url('/my');
                    $menu->add($branchlabel, $branchurl, $branchtitle);
                } else {
                    $branchtitle = get_string('home');
                    $branchlabel = '<i class="fa fa-home"></i> '. $branchtitle;
                    $branchurl = new moodle_url('/my');
                    $menu->add($branchlabel, $branchurl, $branchtitle);
                }
            }
            if (!empty($this->page->theme->settings->navbarcalendarlink)) {
                $branchtitle = get_string('calendarlink', 'theme_unamurui');
                $branchlabel = '<i class="fa fa-calendar"></i> ' . $branchtitle;
                $branchurl = new moodle_url('/calendar/view.php?view=month');
                $menu->add($branchlabel, $branchurl, $branchtitle);
            }
            if (!empty($this->page->theme->settings->privatefileslink) &&
                    has_capability('moodle/user:manageownfiles', context_system::instance())) {
                $title = 'Mes Fichiers';
                $label = '<i class="fa fa-folder"></i> ' . $title;
                $url = new moodle_url('/user/files.php', array('returnurl' => $this->page->url->out()));
                $menu->add($label, $url, $title);
            }

            if (!empty($this->page->theme->settings->personalcourselistintoolbar)) {
                $branchtitle = get_string('mycourses', 'theme_receptic');
                $branchlabel = '<i class="fa fa-briefcase"></i> ' . $branchtitle;
                $branchurl = new moodle_url('/my/index.php');

                $branch = $menu->add($branchlabel, $branchurl, $branchtitle);
                if ($mycourses = enrol_get_my_courses(null, 'fullname ASC')) {
                    foreach ($mycourses as $mycourse) {
                        if ($mycourse->visible) {
                            $branch->add(
                                '<i class="fa fa-book"></i> ' . format_string($mycourse->shortname . ' - ' . $mycourse->fullname),
                                new moodle_url('/course/view.php?id=' . $mycourse->id),
                                format_string($mycourse->shortname));
                        } else if (has_capability('moodle/course:viewhiddencourses', context_course::instance($mycourse->id))) {
                            $branch->add('<span class="dimmed_text"><i class="fa fa-eye-slash"></i> ' .
                                format_string($mycourse->fullname) . '</span>',
                                new moodle_url('/course/view.php', array('id' => $mycourse->id)),
                                format_string($mycourse->shortname));
                        }
                    }

                } else {
                    $nocoursesstring = get_string('emptycourselist', 'theme_receptic');

                    $branch->add('<em>' . $nocoursesstring . '</em>', new moodle_url('/'), $nocoursesstring);
                }
                $branch->add(
                    '#######',
                    new moodle_url('/'),
                    '#######'
                );
                $branch->add(
                    '<i class="fa fa-list"></i> ' . get_string('fulllistofcourses'),
                    new moodle_url($CFG->wwwroot . '/course/index.php'),
                    get_string('fulllistofcourses')
                );

                if (substr_count($USER->email, '@student.unamur.be')) {
                    $branch->add(
                        '#######',
                        new moodle_url('/'),
                        '#######'
                    );
                    $branch->add(
                        '<i class="fa fa-plus"></i> ' . 'M\'inscrire aux cours de mon programme...',
                        new moodle_url($CFG->wwwroot . '/local/unamur/noe/enrolnoecourses.php'),
                        'M\'inscre aux cours de mon programme'
                    );
                }
                if (true) {//(has_capability('local/createcourse:create', context_system::instance())) {
                    $branch->add(
                        '#######',
                        new moodle_url('/'),
                        '#######'
                    );

                    $branch->add(
                        '<i class="fa fa-plus"></i> ' . 'Créer un cours...',
                        new moodle_url($CFG->wwwroot . '/local/createcourse/index.php'),
                        'Créer un cours'
                    );
                }
                if (has_capability('moodle/course:create', context_system::instance())) {
                    $branch->add(
                        '<i class="fa fa-plus"></i> ' . 'Créer un cours... (manuel)',
                        new moodle_url($CFG->wwwroot . '/course/edit.php?category=1&returnto=topcat'),
                        'Créer un cours'
                    );
                }

            }


        }

        $langs = get_string_manager()->get_list_of_translations();
        $haslangmenu = $this->lang_menu() != '';

        if (!$menu->has_children() && !$haslangmenu) {
            return '';
        }

        if ($haslangmenu) {
            $strlang = get_string('language');
            $currentlang = current_language();
            if (isset($langs[$currentlang])) {
                $currentlang = $langs[$currentlang];
            } else {
                $currentlang = $strlang;
            }
            $this->language = $menu->add($currentlang, new moodle_url('#'), $strlang, 10000);
            foreach ($langs as $langtype => $langname) {
                $this->language->add($langname, new moodle_url($this->page->url, array('lang' => $langtype)), $langname);
            }
        }

        $content = '';
        foreach ($menu->get_children() as $item) {
            $settings = external_settings::get_instance();
            $settings->set_raw(true);
            $context = $item->export_for_template($this);

            $content .= $this->render_from_template('theme_receptic/mycustom_menu_item', $context);
        }

        return $content;
    }

    public function admin_link() {
        $admin = $this->page->settingsnav->find('siteadministration', navigation_node::TYPE_SITE_ADMIN);
        if (!$admin) {
            // Try again - crazy nav tree!
            $admin = $this->page->settingsnav->find('root', navigation_node::TYPE_SITE_ADMIN);
        }
        //print_object($admin);
        if ($admin) {
            $title = 'Admin';
            $label = '<i class="fa fa-cog"> </i>' . $title;
            $url = $admin->action;
            $menuitem = new custom_menu_item($label, $url, $title);
            $context = $menuitem->export_for_template($this);
            return $this->render_from_template('theme_receptic/mycustom_menu_item', $context);
        }
    }

    public function mycourse_settings_menu() {
        $context = $this->page->context;
        $menu = new action_menu();

        $items = $this->page->navbar->get_items();
        $currentnode = end($items);

        $showcoursemenu = true;
        $showfrontpagemenu = false;
        $showusermenu = false;

        // We are on the course home page.
        if (($context->contextlevel == CONTEXT_COURSE || $context->contextlevel == CONTEXT_MODULE)) {
            $showcoursemenu = true;
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

        return $this->render_myaction_menu($menu);
    }

    public function render_myaction_menu(action_menu $menu) {

        // We don't want the class icon there!
        foreach ($menu->get_secondary_actions() as $action) {
            if ($action instanceof \action_menu_link && $action->has_class('icon')) {
                $action->attributes['class'] = preg_replace('/(^|\s+)icon(\s+|$)/i', '', $action->attributes['class']);
            }
        }

        if ($menu->is_empty()) {
            return '';
        }
        $context = $menu->export_for_template($this);

        // We do not want the icon with the caret, the caret is added by Bootstrap.
        if (empty($context->primary->menutrigger)) {
            $newurl = $this->pix_url('t/edit', 'moodle');
            $context->primary->icon['attributes'] = array_reduce($context->primary->icon['attributes'],
                function($carry, $item) use ($newurl) {
                    if ($item['name'] === 'src') {
                        $item['value'] = $newurl->out(false);
                    }
                    $carry[] = $item;
                    return $carry;
                }, []
            );
        }
        $context->primary->actiontext = '<i class="fa fa-cog"></i>' . $this->page->course->shortname;
        $context->classes .= ' nav-item action-menu-in-navbar';
        $context->primary->icon = '';


        return $this->render_from_template('core/action_menu', $context);
    }

    /**
     * Take a node in the nav tree and make an action menu out of it.
     * The links are injected in the action menu.
     * Cloned from parent because needed in custom method "mycourse_settings_menu" and has private access in parent class.
     *
     * @param action_menu $menu
     * @param navigation_node $node
     * @param boolean $indent
     * @param boolean $onlytopleafnodes
     * @return boolean nodesskipped - True if nodes were skipped in building the menu
     */
    private function build_action_menu_from_navigation(action_menu $menu,
                                                       navigation_node $node,
                                                       $indent = false,
                                                       $onlytopleafnodes = false) {
        $skipped = false;
        // Build an action menu based on the visible nodes from this navigation tree.
        foreach ($node->children as $menuitem) {
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
            $addblock = '<div class="add_block_button">' .
                    '<a class="btn btn-default" href="' . $url->out() . '" data-key="addblock" >' .
                    '<i class="fa fa-plus"></i> ' .
                    get_string('addblock') .
                    '</a></div>';
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

}
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
 * .
 *
 * @package    theme_receptic
 * @author     Jean-Roch Meurisse
 * @copyright  2016 - Cellule TICE - Unversite de Namur
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['advancedsettings'] = 'Advanced settings';
$string['brandbanner'] = 'Brand banner';
$string['brandbanner_desc'] = 'Display brand banner with logo(s) defined in theme general settings';
$string['brandbannercolor'] = 'Brand banner color';
$string['brandbannercolor_desc'] = 'Base color for brand banner gradient';
$string['brandcolor'] = 'Brand color';
$string['brandcolor_desc'] = 'The accent colour';
$string['branding'] = 'Branding';
$string['choosereadme'] = 'Receptic is a Boost child theme offering extended functionality. Among others it offers such called redballs that allow users to get a visual hint on recently added resources and activities. It also proposes, among other things, an optional simplified dashboard, a brand banner, flash messages for both teachers and students a steady edit button in the navbar';
$string['configtitle'] = 'Receptic';
$string['generalsettings'] = 'General settings';
$string['loginbackgroundimage'] = 'Login page background image';
$string['loginbackgroundimage_desc'] = 'Your image will be stretched to fill the background of the login page';
$string['pluginname'] = 'Receptic';
$string['preset'] = 'Theme preset';
$string['preset_desc'] = 'Pick a preset to broadly change the look of the theme.';
$string['presetfiles'] = 'Additional theme preset files';
$string['presetfiles_desc'] = 'Preset files can be used to dramatically alter the appearance of the theme. See <a href=https://docs.moodle.org/dev/Boost_Presets>Boost presets</a> for information on creating and sharing your own preset files, and see the <a href=http://moodle.net/boost>Presets repository</a> for presets that others have shared.';
$string['rawscss'] = 'Raw SCSS';
$string['rawscss_desc'] = 'Use this field to provide SCSS or CSS code which will be injected at the end of the style sheet';
$string['rawscsspre'] = 'Raw initial SCSS';
$string['rawscsspre_desc'] = 'In this field you can provide initialising SCSS code, it will be injected before everything else. Most of the time you will use this setting to define variables.';
$string['region-side-pre'] = 'Right';
$string['otheractivities'] = 'Other activities';
$string['otherresources'] = 'Other resources';
$string['mainmodules'] = 'Main tools';
$string['toolselector'] = 'Activities/resources selector';
$string['activitieslist'] = 'Main activity types';
$string['activitieslist_desc'] = 'Select activity types to display on top of tools selector in a "main section"';
$string['resourceslist'] = 'Main resource types';
$string['resourceslist_desc'] = 'Select resource types to display on top of tools selector in a "main section"';
$string['hideblocks'] = 'Hide blocks';
$string['showblocks'] = 'Show blocks';
$string['editmode'] = 'Edit mode';
$string['toolbarsettings'] = 'Toolbar settings';
$string['emptycourselist'] = 'No courses';
$string['mycourses'] = 'My courses';
$string['addsection'] = 'Add new section';
$string['unenrolme'] = 'Unenroll me from this course.';
$string['createcourse'] = 'Create course... (manual)';
$string['editbutton'] = 'Display edit button';
$string['editbuttondesc'] = 'Add an edit button in awesomebar';
$string['hidedefaulteditingbutton'] = 'Hide default edit button';
$string['hidedefaulteditingbutton_desc'] = 'Hide default contextual edit button (inactive if edit button in navigation bar is disabled)';
$string['connectme'] = 'Log me in';
$string['redballs'] = 'Red balls';
$string['enableballs'] = 'Enable red and orange balls';
$string['enableballs_desc'] = 'Display the number of "not already seen" resources and activities in user dashboard and section names; pin "not already seen activities and resources in course home page<br/>Display the number of resources and activities with modified content in user dashboard and section names; pin "content-updated activities and resources in course home page';
$string['hotitemslookback'] = 'Red/orange balls look back period';
$string['hotitemslookback_desc'] = 'Number of days or weeks to take into account when computing red/orange balls';
$string['logocenter'] = 'Center logo for banner';
$string['logocenter_desc'] = 'Your image will be placed over the banner on the center';
$string['logoleft'] = 'Left logo for banner';
$string['logoleft_desc'] = 'Your image will be placed over the banner on the lefthand side (plaftorm logo)';
$string['logoright'] = 'Right logo for banner';
$string['logoright_desc'] = 'Your image will be placed over the banner on the righthand side (institution logo)';
$string['receptic:editflashbox'] = 'Capability to edit flashboxes';
$string['flashboxes'] = 'Flashboxes';
$string['flashbox1'] = 'Flashbox 1';
$string['flashbox1_desc'] = 'Flash message number 1 that will appear on target users\'s dashboard';
$string['flashboxtype'] = 'Flashbox type';
$string['flashboxtype_desc'] = 'Type of message to display in this flashbox';
$string['flashbox2'] = 'Flashbox 2';
$string['flashbox2_desc'] = 'Flash message number 2 that will appear on target users\'s dashboard';
$string['flashboxforall'] = 'All users';
$string['flashboxforall_desc'] = 'Display this flashbox to all users (cohort filter will not be applied)';
$string['flashboxcohorts'] = 'Target cohort(s)';
$string['flashboxcohorts_desc'] = 'Display this flashbox to selected cohort(s) only';
$string['trick'] = 'Trick';
$string['showblocks'] = 'Show blocks';
$string['hideblocks'] = 'Hide blocks';
$string['addcoursebutton'] = 'Add course button';
$string['addcoursebutton_desc'] = 'Add a button to create a course on course creators\' dashboard';
$string['localcreatecourseplugin'] = 'Local create course plugin';
$string['localcreatecourseplugin_desc'] = 'Name of a local plugin implementing an alternative method to create a course (for instance if teachers are only allowed to create courses that exist in an official course list). If such a plugin exists it must declare local/*yourpluginname*:create capability';
$string['bulkenrolme'] = 'Bulk enrol me button';
$string['bulkenrolme_desc'] = 'Add a button to allow students to enrol to an official course list (requires a local plugin specific to your insitution).';
$string['bulkenrolmeplugin'] = 'Bulk enrolment plugin';
$string['bulkenrolmeplugin_desc'] = 'Name of a local plugin file implementing a bulk enrolment for students (for example enrol_bulkenrol)';
$string['bulkenrolmefile'] = 'Plugin file name';
$string['bulkenrolmefile_desc'] = 'Name of the plugin file to use to trigger bulk enrolment of a student to their courses';
$string['bulkenrolemailpattern'] = 'Email pattern';
$string['bulkenrolemailpattern_desc'] = 'To restrict the local bulk enrolment method to students with an email pattern';
$string['dashboarddisplaymode'] = 'Display mode';
$string['coursecreation'] = 'Adding course';
$string['bulkenrol'] = 'Bulk enrolment';
$string['otherdashboardelements'] = 'Other shortcuts on dashboard';
$string['togglecoursevisibility'] = 'Toggle course visibility';
$string['togglecoursevisibility_desc'] = 'Allow teachers to toggle course visibility from their dashboard.';
$string['unenrolme'] = 'Unenrol me';
$string['unenrolme_desc'] = 'Allow users to unenrol from courses on their dashboard.';
$string['showprogress'] = 'Show progress';
$string['showprogress_desc'] = 'Show progress in course list';
$string['footer'] = 'Footer';
$string['contactheader'] = 'Contact header';
$string['contactheader_desc'] = 'Add a header above contact information';
$string['contactemail'] = 'Contact email';
$string['contactemail_desc'] = 'Add an email link in page footer to contact help service (no email link if left empty).';
$string['contactphone'] = 'Contact phone number';
$string['contactphone_desc'] = 'Add a contact phone number in page footer (nothing if left empty).';
$string['moodlecredits'] = 'Moodle credits';
$string['moodlecredits_desc'] = 'Add Moodle logo and "Powered by Moodle" in page footer';
$string['or'] = 'or';
$string['poweredby'] = 'Powered by';
$string['logininfo'] = 'Connected user';
$string['logininfo_desc'] = 'Display a link to the connected user\' profile and a logout link in the page footer';

// Block My overview.
$string['dashboardcoursefiltering'] = 'Course filtering';
$string['allowcoursefiltering'] = 'Display filters';
$string['allowcoursefiltering_desc'] = 'Allow users to filter courses. If set to no, filter settings below will have no effect';
$string['coursefilterall_desc'] = 'Filter to display all current user\'s courses';
$string['coursefilterinprogress_desc'] = 'Filter to display courses in progress only';
$string['coursefilterfuture_desc'] = 'Filter to display future courses only';
$string['coursefilterpast_desc'] = 'Filter to display past courses only';
$string['coursefilterstarred_desc'] = 'Filter to display starred courses only';
$string['coursefilterhidden_desc'] = 'Filter to display hidden courses only';
$string['dashboardcoursesorting'] = 'Course sorting';
$string['allowcoursesorting'] = 'Display sorting options';
$string['allowcoursesorting_desc'] = 'Allow users to sort courses by name/last accessed';
$string['dashboarddisplaymode'] = 'Display mode for course list';
$string['allowdisplaymode'] = 'Course display mode';
$string['allowdisplaymode_desc'] = 'Allow users to choose a display mode (list/tiles/summary)';

$string['makevisible'] = 'Make visible';
$string['confirmmakevisible'] = 'Confirm to allow students to view your course';
$string['makeinvisible'] = 'Make invisible';
$string['confirmmakeinvisible'] = 'Confirm to prevent access to your course';
$string['confirmunenrolme'] = 'Do you really want to unenrol from this course?';
$string['activitynavigation'] = 'Activity navigation';
$string['activitynavigation_desc'] = 'Display a select box under activities allowing to jump to another activity without going back to course home page';
$string['helptextinmodal'] = 'Help texts in modal';
$string['helptextinmodal_desc'] = 'Display help text in a modal window rather than default popover';
$string['settingsincoursepage'] = 'Settings menu in course page';
$string['settingsincoursepage_desc'] = 'This setting replaces dropdown menus for course and activity administration with the full administration menu directly beneath the course header when clicking the cog icon. This setting also hides the settings icon on the participants page as the entries on this page are duplicated with the in-course course menu and therefore not necessary.
Please note that this change does not affect users who have switched off javascript in their browsers - they will still get the behaviour from Moodle core with a popup course context menu.';
$string['showhiddencoursewarning'] = 'Display warning in hidden courses';
$string['showhiddencoursewarning_desc'] = 'If set a warning will be displayed in the course header as long as the visibility of the course is hidden. A shorcut to make the course visible directly without going to course paramteres page is also proposed.';
$string['showswitchrolewarning'] = 'Switch role information in warning box';
$string['showswitchrolewarning_desc'] = 'If set the information to which role a user has switched is being displayed in a warning box rather than in the user menu (like in theme Boost). A link to switch back will be proposed.';
$string['switchedroleto'] = 'You are viewing this course currently with the role:';
$string['shrinkablenavdrawer'] = 'Shrinkable navigation drawer';
$string['shrinkablenavdrawer_desc'] = 'Replace default nav drawer behaviour (hide/show) with a shrinked version (display only first level icons) when drawer is "closed"';
$string['privacy:metadata:preference:blockscolumnopen'] = 'The user\'s preference for hiding or showing the blocks column.';
$string['privacy:blockscolumnopentrue'] = 'The current preference for the blocks column is closed.';
$string['privacy:blockscolumnopenfalse'] = 'The current preference for the blocks column is open.';
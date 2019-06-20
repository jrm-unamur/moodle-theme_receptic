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

$string['activitieslist'] = 'Main activity types';
$string['activitieslist_desc'] = 'Select activity types to display on top of tools selector in a "main section"';
$string['activitynavigation'] = 'Activity navigation';
$string['activitynavigation_desc'] = 'Display a select box under activities allowing to jump to another activity without going back to course home page';
$string['addcoursebutton'] = 'Add course button';
$string['addcoursebutton_desc'] = 'Add a button to create a course on course creators\' dashboard';
$string['allowcoursefiltering'] = 'Display filters';
$string['allowcoursefiltering_desc'] = 'Allow users to filter courses. If set to no, filter settings below will have no effect';
$string['allowcoursesorting'] = 'Display sorting options';
$string['allowcoursesorting_desc'] = 'Allow users to sort courses by name/last accessed';
$string['allowdisplaymode'] = 'Course display mode';
$string['allowdisplaymode_desc'] = 'Allow users to choose a display mode (list/tiles/summary)';
$string['brandbanner'] = 'Brand banner';
$string['brandbanner_desc'] = 'Display brand banner with logo(s) defined in theme general settings';
$string['brandbannercolor'] = 'Brand banner color';
$string['brandbannercolor_desc'] = 'Base color for brand banner gradient';
$string['brandcolor'] = 'Brand color';
$string['brandcolor_desc'] = 'The accent colour';
$string['branding'] = 'Branding';
$string['bulkenrol'] = 'Bulk enrolment';
$string['bulkenrolme'] = 'Bulk enrol me button';
$string['bulkenrolme_desc'] = 'Add a button to allow students to enrol to an official course list (requires a local plugin specific to your insitution).';
$string['bulkenrolmefile'] = 'Plugin file name';
$string['bulkenrolmefile_desc'] = 'Name of the plugin file to use to trigger bulk enrolment of a student to their courses';
$string['bulkenrolmeplugin'] = 'Bulk enrolment plugin';
$string['bulkenrolmeplugin_desc'] = 'Name of a local plugin file implementing a bulk enrolment for students (for example enrol_bulkenrol)';
$string['bulkenrolemailpattern'] = 'Email pattern';
$string['bulkenrolemailpattern_desc'] = 'To restrict the local bulk enrolment method to students with an email pattern';
$string['choosereadme'] = 'Receptic is a Boost child theme offering extended functionality. Among others it offers such called redballs that allow users to get a visual hint on recently added resources and activities. It also proposes, among other things, an optional simplified dashboard, a brand banner, flash messages for both teachers and students a steady edit button in the navbar';
$string['configtitle'] = 'Receptic';
$string['confirmmakeinvisible'] = 'Confirm to prevent access to your course';
$string['confirmmakevisible'] = 'Confirm to allow students to view your course';
$string['confirmunenrolme'] = 'Do you really want to unenrol from this course?';
$string['connectme'] = 'Log me in';
$string['contactemail'] = 'Contact email';
$string['contactemail_desc'] = 'Add an email link in page footer to contact help service (no email link if left empty).';
$string['contactheader'] = 'Contact header';
$string['contactheader_desc'] = 'Add a header above contact information';
$string['contactphone'] = 'Contact phone number';
$string['contactphone_desc'] = 'Add a contact phone number in page footer (nothing if left empty).';
$string['coursecreation'] = 'Adding course';
$string['coursefilterall_desc'] = 'Filter to display all current user\'s courses';
$string['coursefilterfuture_desc'] = 'Filter to display future courses only';
$string['coursefilterhidden_desc'] = 'Filter to display hidden courses only';
$string['coursefilterinprogress_desc'] = 'Filter to display courses in progress only';
$string['coursefilterpast_desc'] = 'Filter to display past courses only';
$string['coursefilterstarred_desc'] = 'Filter to display starred courses only';
$string['courseisnowvisible'] = 'This course is now visible to enrolled students.';
$string['createcourse'] = 'Create course... (manual)';
$string['dashboardcoursefiltering'] = 'Course filtering';
$string['dashboardcoursesorting'] = 'Course sorting';
$string['dashboarddisplaymode'] = 'Display mode for course list';
$string['disableavatarupload'] = 'Disable avatar upload';
$string['disableavatarupload_desc'] = 'Activiate this setting to prevent users from uploading a profile picture or avatar. It can be helpful if your want to collect users\' pictures form an external system.';
$string['disableavataruploademailpattern'] = 'Email patterns for this restriction';
$string['disableavataruploademailpattern_desc'] = 'If "disableavatarupload" is set, use this field to limit the restriction to some email address patterns. Add one email pattern per line. All matching users won\'t be able to upload their profile picture. ';
$string['editbutton'] = 'Display edit button';
$string['editbutton_desc'] = 'Add an edit button in navbar';
$string['editmode'] = 'Edit mode';
$string['emptycourselist'] = 'No courses';
$string['enableballs'] = 'Enable red and orange balls';
$string['enableballs_desc'] = 'Display the number of "not already seen" resources and activities in user dashboard and section names; pin "not already seen activities and resources in course home page<br/>Display the number of resources and activities with modified content in user dashboard and section names; pin "content-updated activities and resources in course home page';
$string['extrajsmodule'] = 'External AMD module';
$string['extrajsmodule_desc'] = 'Use this field to request an AMD module defined in another plugin. The value of this field must match "frankenstyle_path/your_js_filename" pattern, par exemple "local_yourplugin/your_amd_filename';
$string['extrajsmoduleargs'] = 'Extra AMD module parameters';
$string['extrajsmoduleargs_desc'] = 'You can add here parameters (in JSON format) that will be passed to your extra AMD module';
$string['flashboxcohorts'] = 'Target cohort(s)';
$string['flashboxcohorts_desc'] = 'Display this flashbox to selected cohort(s) only';
$string['flashboxdismissable'] = 'Dismissable';
$string['flashboxdismissable_desc'] = 'If checked, users will be able to dismiss the message, otherwise the message will be deactivated when an administrator decides to deactivate it for all target users';
$string['flashboxes'] = 'Flashboxes';
$string['flashboxforall'] = 'All users';
$string['flashboxforall_desc'] = 'Display this flashbox to all users (cohort filter will not be applied)';
$string['flashboxtype'] = 'Flashbox type';
$string['flashboxtype_desc'] = 'Type of message to display in this flashbox';
$string['flashbox1'] = 'Flashbox 1';
$string['flashbox1_desc'] = 'Flash message number 1 that will appear on target users\'s dashboard';
$string['flashbox2'] = 'Flashbox 2';
$string['flashbox2_desc'] = 'Flash message number 2 that will appear on target users\'s dashboard';
$string['footer'] = 'Footer';
$string['forceddisplaymode'] = 'Default display mode';
$string['forceddisplaymode_desc'] = 'If display mode chooser is deactivated for users, choose a display mode for everyone.';
$string['generalsettings'] = 'General settings';
$string['helptextinmodal'] = 'Help texts in modal';
$string['helptextinmodal_desc'] = 'Display help text in a modal window rather than default popover';
$string['hiddencoursewarning'] = 'This course is currently <strong>hidden</strong>. Only enrolled teachers can access this course when hidden.';
$string['hideblocks'] = 'Hide blocks';
$string['hidedefaulteditingbutton'] = 'Hide default edit button';
$string['hidedefaulteditingbutton_desc'] = 'Hide default contextual edit button (inactive if edit button in navigation bar is disabled)';
$string['hotitemslookback'] = 'Red/orange balls look back period';
$string['hotitemslookback_desc'] = 'Number of days or weeks to take into account when computing red/orange balls';
$string['localcreatecourseplugin'] = 'Local create course plugin';
$string['localcreatecourseplugin_desc'] = 'Name of a local plugin implementing an alternative method to create a course (for instance if teachers are only allowed to create courses that exist in an official course list). If such a plugin exists it must declare local/*yourpluginname*:create capability';
$string['logininfo'] = 'Connected user';
$string['logininfo_desc'] = 'Display a link to the connected user\' profile and a logout link in the page footer';
$string['logocenter'] = 'Center logo for banner';
$string['logocenter_desc'] = 'Your image will be placed over the banner on the center';
$string['logocenteralt'] = '"Alt" attribute for center logo (if any)';
$string['logocenterturl'] = 'Target url for center logo (if any)';
$string['logoleft'] = 'Left logo for banner';
$string['logoleft_desc'] = 'Your image will be placed over the banner on the lefthand side (plaftorm logo)';
$string['logoleftalt'] = '"Alt" attribute for left logo (if any)';
$string['logolefturl'] = 'Target url for left logo (if any)';
$string['logoright'] = 'Right logo for banner';
$string['logoright_desc'] = 'Your image will be placed over the banner on the righthand side (institution logo)';
$string['logorighturl'] = 'Target url for right logo (if any)';
$string['logorightalt'] = '"Alt" attribute for right logo (if any)';
$string['mainmodules'] = 'Main tools';
$string['makecoursevisible'] = 'Make this course available to enrolled students.';
$string['makeinvisible'] = 'Make invisible';
$string['makevisible'] = 'Make visible';
$string['moodlecredits'] = 'Moodle credits';
$string['moodlecredits_desc'] = 'Add Moodle logo and "Powered by Moodle" in page footer';
$string['mycourses'] = 'My courses';
$string['nopictureupload'] = 'To upload a profile picture, use <a href="http://www.unamur.be/apps-photo" target="_blank">the official picture uploader </a >. Your picture will then be available in WebCampus as well as in your personal administrative file';
$string['noreplywarning'] = '!!! Please do not reply to this email !!!<br/>Messages will no be processed';
$string['or'] = 'or';
$string['otheractivities'] = 'Other activities';
$string['otherdashboardelements'] = 'Other shortcuts on dashboard';
$string['otherresources'] = 'Other resources';
$string['pluginname'] = 'Receptic';
$string['poweredby'] = 'Powered by';
$string['preset'] = 'Theme preset';
$string['preset_desc'] = 'Pick a preset to broadly change the look of the theme.';
$string['presetfiles'] = 'Additional theme preset files';
$string['presetfiles_desc'] = 'Preset files can be used to dramatically alter the appearance of the theme. See <a href=https://docs.moodle.org/dev/Boost_Presets>Boost presets</a> for information on creating and sharing your own preset files, and see the <a href=http://moodle.net/boost>Presets repository</a> for presets that others have shared.';
$string['privacy:blockscolumnopenfalse'] = 'The current preference for the blocks column is open.';
$string['privacy:blockscolumnopentrue'] = 'The current preference for the blocks column is closed.';
$string['privacy:metadata:preference:blockscolumnopen'] = 'The user\'s preference for hiding or showing the blocks column.';
$string['rawscss'] = 'Raw SCSS';
$string['rawscss_desc'] = 'Use this field to provide SCSS or CSS code which will be injected at the end of the style sheet';
$string['rawscsspre'] = 'Raw initial SCSS';
$string['rawscsspre_desc'] = 'In this field you can provide initialising SCSS code, it will be injected before everything else. Most of the time you will use this setting to define variables.';
$string['receptic:editflashbox'] = 'Capability to edit flashboxes';
$string['redballs'] = 'Red balls';
$string['region-side-pre'] = 'Right';
$string['resourceslist'] = 'Main resource types';
$string['resourceslist_desc'] = 'Select resource types to display on top of tools selector in a "main section"';
$string['settingsincoursepage'] = 'Settings menu in course page';
$string['settingsincoursepage_desc'] = 'This setting replaces dropdown menus for course and activity administration with the full administration menu directly beneath the course header when clicking the cog icon. This setting also hides the settings icon on the participants page as the entries on this page are duplicated with the in-course course menu and therefore not necessary.
Please note that this change does not affect users who have switched off javascript in their browsers - they will still get the behaviour from Moodle core with a popup course context menu.';
$string['showblocks'] = 'Show blocks';
$string['showhiddencoursewarning'] = 'Display warning in hidden courses';
$string['showhiddencoursewarning_desc'] = 'If set a warning will be displayed in the course header as long as the visibility of the course is hidden. A shorcut to make the course visible directly without going to course paramteres page is also proposed.';
$string['showprogress'] = 'Show progress';
$string['showprogress_desc'] = 'Show progress in course list';
$string['showswitchedrolewarning'] = 'Switch role information in warning box';
$string['showswitchedrolewarning_desc'] = 'If set the information to which role a user has switched is being displayed in a warning box rather than in the user menu (like in theme Boost). A link to switch back will be proposed.';
$string['shrinkablenavdrawer'] = 'Shrinkable navigation drawer';
$string['shrinkablenavdrawer_desc'] = 'Replace default nav drawer behaviour (hide/show) with a shrinked version (display only first level icons) when drawer is "closed"';
$string['switchedroleto'] = 'You are viewing this course currently with the role:';
$string['togglecoursevisibility'] = 'Toggle course visibility';
$string['togglecoursevisibility_desc'] = 'Allow teachers to toggle course visibility from their dashboard. If activated users will not be able to hide courses from their list.';
$string['toolbarsettings'] = 'Toolbar settings';
$string['toolselector'] = 'Activities/resources selector';
$string['trick'] = 'Trick';
$string['unenrolme'] = 'Unenrol me';
$string['unenrolme_desc'] = 'Allow users to unenrol from courses on their dashboard.';
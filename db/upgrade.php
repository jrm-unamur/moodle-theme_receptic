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
 * Theme receptic upgrade steps are defined here.
 *
 * @package    theme_receptic
 * @author     Jean-Roch Meurisse
 * @copyright  2016 - Cellule TICE - Unversite de Namur
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Execute theme_receptic upgrade from the given old version
 *
 * @param int $oldversion
 * @return bool
 */
function xmldb_theme_receptic_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager(); // Loads ddl manager and xmldb classes.

    if ($oldversion < 2021090300) {
        // Define table theme_receptic_filtered_log to be created.
        $table = new xmldb_table('theme_receptic_filtered_log');

        // Adding fields to table theme_receptic_filtered_log.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('eventname', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('component', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('action', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('target', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('objecttable', XMLDB_TYPE_CHAR, '50', null, null, null, null);
        $table->add_field('objectid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('crud', XMLDB_TYPE_CHAR, '1', null, XMLDB_NOTNULL, null, null);
        $table->add_field('edulevel', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, null);
        $table->add_field('contextid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('contextlevel', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('contextinstanceid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('relateduserid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('anonymous', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('other', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('origin', XMLDB_TYPE_CHAR, '10', null, null, null, null);
        $table->add_field('ip', XMLDB_TYPE_CHAR, '45', null, null, null, null);
        $table->add_field('realuserid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);

        // Adding keys to table theme_receptic_filtered_log.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('contextid', XMLDB_KEY_FOREIGN, ['contextid'], 'context', ['id']);

        // Adding indexes to table theme_receptic_filtered_log.
        $table->add_index('timecreated', XMLDB_INDEX_NOTUNIQUE, ['timecreated']);
        $table->add_index('course-time', XMLDB_INDEX_NOTUNIQUE, ['courseid', 'anonymous', 'timecreated']);
        $table->add_index('user-module', XMLDB_INDEX_NOTUNIQUE, ['userid', 'contextlevel', 'contextinstanceid', 'crud', 'edulevel', 'timecreated']);

        // Conditionally launch create table for theme_receptic_filtered_log.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }
        //theme_receptic_import_logstore();

        // Theme receptic savepoint reached.
        upgrade_plugin_savepoint(true, 2021090300, 'theme', 'receptic');
    }
    return true;
}

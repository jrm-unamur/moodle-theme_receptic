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
 * Privacy tests for theme_receptic.
 *
 * @package    theme_receptic
 * @author     Jean-Roch Meurisse
 * @copyright  2016 - Cellule TICE - Unversite de Namur
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use \theme_receptic\privacy\provider;

/**
 * Unit tests for theme_receptic/classes/privacy/policy
 *
 * Adapted from boost unit tests to handle blocks-column-open preference
 * @author     Jean-Roch Meurisse
 * @copyright  2016 - Cellule TICE - Unversite de Namur
 * @copyright  2018 Adrian Greeve <adriangreeve.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class theme_receptic_privacy_testcase extends \core_privacy\tests\provider_testcase {

    /**
     * Test for provider::test_export_user_preferences().
     */
    public function test_export_user_preferences() {
        $this->resetAfterTest();

        // Test setup.
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        // Add a user home page preference for the User.
        set_user_preference(provider::BLOCKS_COLUMN_OPEN, 'false', $user);

        // Test the user preferences export contains 1 user preference record for the User.
        provider::export_user_preferences($user->id);
        $contextuser = context_user::instance($user->id);
        $writer = \core_privacy\local\request\writer::with_context($contextuser);
        $this->assertTrue($writer->has_any_data());

        $exportedpreferences = $writer->get_user_preferences('theme_receptic');
        $this->assertCount(1, (array) $exportedpreferences);
        $this->assertEquals('false', $exportedpreferences->{provider::BLOCKS_COLUMN_OPEN}->value);
        $this->assertEquals(get_string('privacy:blockscolumnopenfalse', 'theme_receptic'),
                $exportedpreferences->{provider::BLOCKS_COLUMN_OPEN}->description);

        // Add a user home page preference for the User.
        set_user_preference(provider::BLOCKS_COLUMN_OPEN, 'true', $user);

        // Test the user preferences export contains 1 user preference record for the User.
        provider::export_user_preferences($user->id);
        $contextuser = context_user::instance($user->id);
        $writer = \core_privacy\local\request\writer::with_context($contextuser);
        $this->assertTrue($writer->has_any_data());

        $exportedpreferences = $writer->get_user_preferences('theme_receptic');
        $this->assertCount(1, (array) $exportedpreferences);
        $this->assertEquals('true', $exportedpreferences->{provider::BLOCKS_COLUMN_OPEN}->value);
        $this->assertEquals(get_string('privacy:blockscolumnopentrue', 'theme_receptic'),
                $exportedpreferences->{provider::BLOCKS_COLUMN_OPEN}->description);
    }
}

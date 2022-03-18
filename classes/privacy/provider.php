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
 * Privacy Subsystem implementation for theme_boost.
 *
 * @package    theme_receptic
 * @copyright  2016 - Cellule TICE - Unversite de Namur
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_receptic\privacy;

use \core_privacy\local\metadata\collection;

/**
 * The receptic theme stores a user preference data. Adaptation of boost's privacy provider by Andrew Nichols.
 *
 * @package    theme_receptic
 * @author     Jean-Roch Meurisse
 * @copyright  2016 - Cellule TICE - Unversite de Namur
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    // This plugin has data.
    \core_privacy\local\metadata\provider,
    // This plugin has some sitewide user preferences to export.
    \core_privacy\local\request\user_preference_provider {

    /** The user preference for the blocks column. */
    const BLOCKS_COLUMN_OPEN = 'blocks-column-open';

    /**
     * Returns meta data about this system.
     *
     * @param  collection $items The initialised item collection to add items to.
     * @return collection A listing of user data stored through this system.
     */
    public static function get_metadata(collection $items) : collection {
        $items->add_user_preference(self::BLOCKS_COLUMN_OPEN, 'privacy:metadata:preference:blockscolumnopen');
        return $items;
    }

    /**
     * Store all user preferences for the plugin.
     *
     * @param int $userid The userid of the user whose data is to be exported.
     */
    public static function export_user_preferences(int $userid) {
        $blockscolumnopenpref = get_user_preferences(self::BLOCKS_COLUMN_OPEN, null, $userid);

        if (isset($blockscolumnopenpref)) {
            $preferencestring = get_string('privacy:blockscolumnopenfalse', 'theme_receptic');
            if ($blockscolumnopenpref == 'true') {
                $preferencestring = get_string('privacy:blockscolumnopentrue', 'theme_receptic');
            }
            \core_privacy\local\request\writer::export_user_preference(
                'theme_receptic',
                self::BLOCKS_COLUMN_OPEN,
                $blockscolumnopenpref,
                $preferencestring
            );
        }
    }
}

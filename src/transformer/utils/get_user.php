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
 * Transformer utility for retrieving user data.
 *
 * @package   logstore_xapi
 * @copyright Jerret Fowler <jerrett.fowler@gmail.com>
 *            Ryan Smith <https://www.linkedin.com/in/ryan-smith-uk/>
 *            David Pesce <david.pesce@exputo.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace src\transformer\utils;

/**
 * Transformer utility for retrieving user data.
 *
 * @param array $config The transformer config settings.
 * @param \stdClass $user The user object.
 * @return array
 */
function get_user(array $config, \stdClass $user) {
    $fullname = get_full_name($user);

    $hasvalidemail = filter_var($user->email, FILTER_VALIDATE_EMAIL);

    $toReturn = [];

    if (PHPUNIT_TEST) {
        // Send is tested and should be sent in unit tests.
        $config['send_name'] = true;
    }

    if(array_key_exists('send_name', $config) && $config['send_name'] == true) {
		$toReturn['name'] = $fullname;
	}

    if (array_key_exists('send_mbox', $config) && $config['send_mbox'] == true && $hasvalidemail) {

        $toReturn['objectType'] = ['Agent'];

		if(array_key_exists('hashmbox', $config) && $config['hashmbox'] == true) {
			$toReturn['mbox_sha1sum'] = sha1('mailto:' . $user->email);
		} else {
			$toReturn['mbox'] = 'mailto:' . $user->email;
		}

        return $toReturn;
    }

    if (array_key_exists('send_username', $config) && $config['send_username'] === true) {
        $toReturn['account'] = [
            'homePage' => $config['app_url'],
            'name' => $user->username,
        ];

        return $toReturn;
    }

    $toReturn['account'] = [
        'homePage' => $config['app_url'],
        'name' => strval($user->id),
    ];

	return $toReturn;
}

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

use logstore_xapi\log\store;

/**
 * Transformer utility for retrieving user data.
 *
 * @param array $config The transformer config settings.
 * @param \stdClass $user The user object.
 * @return array
 */

function get_user(array $config, \stdClass $user): array {

    $fullname = get_full_name($user);
    $email = 'mailto:' . $user->email;
    $username = $user->username;
    $userid = strval($user->id);
    $hasvalidemail = filter_var($user->email, FILTER_VALIDATE_EMAIL);

    if (array_key_exists('send_pseudo', $config) && $config['send_pseudo']) {

        $manager = get_log_manager();
        $store = new store($manager);
        $pseudotext = $store->get_pseudo_text();

        $fullname = sha1(($pseudotext.$fullname));
        $email = sha1(($pseudotext.$email));
        $username = sha1(($pseudotext.$username));
        $userid = sha1(($pseudotext.$userid));
    }

    if (array_key_exists('send_mbox', $config) && $config['send_mbox'] && $hasvalidemail) {
        if (array_key_exists('send_pseudo', $config) && $config['send_pseudo']) {
            $object = [
                'name' => $fullname,
                'mbox_sha1sum' => $email,
            ];
        } else {
            $object = [
                'name' => $fullname,
                'mbox' => $email,
            ];
        }
    } else if (array_key_exists('send_username', $config) && $config['send_username']) {
        $object = [
            'name' => $fullname,
            'account' => [
                'homePage' => $config['app_url'],
                'name' => $username,
            ],
        ];
    } else {
        $object = [
            'name' => $fullname,
            'account' => [
                'homePage' => $config['app_url'],
                'name' => $userid,
            ],
        ];
    }

    if ($user->deleted == 1) {
        $object['name'] = 'deleted user';
    }

    return $object;
}

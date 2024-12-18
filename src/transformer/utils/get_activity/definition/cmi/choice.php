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
 * Transformer utilities for creating CMI choice xAPI Activity object definitions.
 *
 * @package   logstore_xapi
 * @copyright Milt Reder <milt@yetanalytics.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace src\transformer\utils\get_activity\definition\cmi;

use src\transformer\utils as utils;

/**
 * Transformer util for creating choice definitions
 *
 * @param array $config The transformer config settings.
 * @param string $name The activity name.
 * @param ?string $description The activity description.
 * @param array $choices The choices available.
 * @param string $lang The language.
 * @param ?array $rightchoices The correct or sequenced options, optional.
 */
function choice(
    array $config,
    string $name,
        ?string $description,
    array $choices,
    string $lang,
        ?array $rightchoices = null
) {
    $cmichoices = ($config['send_response_choices'] && !is_null($choices))
        ? array_map(
            function($choice) use ($lang) {
                return [
                    'id' => utils\slugify($choice),
                    'description' => [
                        $lang => $choice,
                    ],
                ];
            },
            $choices
        )
        : null;

    return [
        ...common($config, $name, $description, $lang),
        'interactionType' => 'choice',
        ...($config['send_response_choices'] && !empty($cmichoices)
            ? [
                'correctResponsesPattern' => [
                    implode(
                        '[,]',
                        // If we are given correct choices, normalize and use
                        !empty($rightchoices)
                            ? array_map(
                                function($rightchoice) {
                                    return utils\slugify($rightchoice);
                                },
                                $rightchoices
                            )
                        // Otherwise, just use the choices (no right answer)
                            : array_map(
                                function($cmichoice) {
                                    return $cmichoice['id'];
                                },
                                $cmichoices
                            )
                    ),
                ],
                'choices' => $cmichoices
            ]
            : []),
    ];
}

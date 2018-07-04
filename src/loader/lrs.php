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

namespace src\loader\lrs;
defined('MOODLE_INTERNAL') || die();

function send_batch_to_lrs(array $config, array $statements) {
    $endpoint = $config['lrs_endpoint'];
    $username = $config['lrs_username'];
    $password = $config['lrs_password'];

    $url = $endpoint.'/statements';
    $auth = base64_encode($username.':'.$password);
    $postdata = json_encode($statements);

    $request = curl_init();
    curl_setopt($request, CURLOPT_URL, $url);
    curl_setopt($request, CURLOPT_POSTFIELDS, $postdata);
    curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($request, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($request, CURLOPT_HTTPHEADER, [
        'Authorization: Basic '.$auth,
        'X-Experience-API-Version: 1.0.0',
        'Content-Type: application/json',
    ]);

    $responsetext = curl_exec($request);
    $responsecode = curl_getinfo($request, CURLINFO_RESPONSE_CODE);
    curl_close($request);

    if ($responsecode !== 200) {
        throw new \Exception($responsetext);
    }
}

function get_statement_batches(array $config, array $statements) {
    $maxbatchsize = $config['lrs_max_batch_size'];
    if (!empty($maxbatchsize) && $maxbatchsize < count($statements)) {
        return array_chunk($statements, $maxbatchsize);
    }
    return [$statements];
}

function load(array $config, array $statements) {
    $batches = get_statement_batches($config, $statements);
    foreach ($batches as $batch) {
        send_batch_to_lrs($config, $batch);
    }
}
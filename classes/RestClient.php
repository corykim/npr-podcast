<?php
/**
 * Created by IntelliJ IDEA.
 * User: cory
 * Date: 10/31/14
 * Time: 12:51 PM
 */

namespace Podcast;


class RestClient {
    protected function rest_call($url) {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $curl_response = curl_exec($curl);

        if ($curl_response === false) {
            $info = curl_getinfo($curl);
            curl_close($curl);
            error_log('error occured during curl exec. Additioanl info: ' . var_export($info));
            return null;
        }
        curl_close($curl);
        $decoded = json_decode($curl_response, true);
        if (isset($decoded->response->status) && $decoded->response->status == 'ERROR') {
            error_log('error occured: ' . $decoded->response->errormessage);
        }

        return $decoded;
    }

} 
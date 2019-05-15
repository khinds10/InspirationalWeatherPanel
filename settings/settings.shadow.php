<?php
// weather API
$weatherAPIURL = 'https://api.darksky.net/forecast/';
$weatherAPIKey = 'MY KEY HERE';
$latitude = '45';
$longitude = '-70';

// temperature color API
$temperatureColorAPI = 'http://my-temperature-api.com';

/**
 * get the response from the API to send to the JS 
 * @param strong $URL
 * @return string, JSON encoded webservice response
 */
function cURL($URL) {

    // is cURL installed yet?
    if (!function_exists('curl_init')) die('Sorry cURL is not installed!');
    
    // download response from URL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $URL);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}

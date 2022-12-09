<?php

namespace App\Service;

class CacheService
{
    public static function purge() {
        $tuCurl = curl_init();
        curl_setopt($tuCurl, CURLOPT_URL, "http://cache");
        curl_setopt($tuCurl, CURLOPT_CUSTOMREQUEST, "PURGE");
        $tuData = curl_exec($tuCurl);
    }
}
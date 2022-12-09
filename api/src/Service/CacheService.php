<?php

namespace App\Service;

class CacheService
{
    public static function purge() {
        $tuCurl = curl_init();
        curl_setopt($tuCurl, CURLOPT_URL, "http://cache");
        curl_setopt($tuCurl, CURLOPT_CUSTOMREQUEST, "PURGE");
        curl_setopt($tuCurl, CURLOPT_RETURNTRANSFER, true);
        $tuData = curl_exec($tuCurl);
        curl_close($tuCurl);
    }
}
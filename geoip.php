<?php
function getCountryFromGeoIP($ip) {
    if (!class_exists('GeoIp2\Database\Reader')) {
        return null;
    }
    
    try {
        $reader = new GeoIp2\Database\Reader(GEOIP_DATABASE);
        $record = $reader->country($ip);
        return $record->country->isoCode;
    } catch (Exception $e) {
        return null;
    }
}
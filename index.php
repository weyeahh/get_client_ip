<?php
require_once 'config.php';
require_once 'database.php';

function getClientIp() {
    foreach (IP_HEADERS as $header) {
        if (isset($_SERVER[$header]) && !empty($_SERVER[$header])) {
            $ips = array_filter(array_map('trim', explode(',', $_SERVER[$header])));
            if (!empty($ips)) {
                return $ips[0];
            }
        }
    }
    return null;
}

function getClientCountry($ip) {
    if (COUNTRY_METHOD === 'header') {
        foreach (COUNTRY_HEADERS as $header) {
            if (isset($_SERVER[$header]) && !empty($_SERVER[$header])) {
                return strtolower($_SERVER[$header]);
            }
        }
    } elseif (COUNTRY_METHOD === 'geoip') {
        if (file_exists(GEOIP_DATABASE)) {
            try {
                require_once 'geoip.php';
                $country = getCountryFromGeoIP($ip);
                if ($country !== null) {
                    return strtolower($country);
                }
            } catch (Exception $e) {
                error_log('GeoIP error: ' . $e->getMessage());
            }
        }
    }
    
    return 'unknown';
}

$host = $_SERVER['HTTP_HOST'] ?? '';
$mode = $_GET['mode'] ?? 'simple';

$rawIp = getClientIp();

if ($rawIp === null) {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'code' => 500,
        'error' => 'Unable to determine client IP address'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$allIps = array_filter(array_map('trim', explode(',', $rawIp)));

if ($host === DOMAIN_IPV4) {
    $ip = null;
    foreach ($allIps as $candidate) {
        if (filter_var($candidate, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $ip = $candidate;
            break;
        }
    }
    if ($ip === null) {
        http_response_code(500);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'code' => 500,
            'error' => 'No IPv4 address found'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    $ipType = 'IPv4';
} elseif ($host === DOMAIN_IPV6) {
    $ip = null;
    foreach ($allIps as $candidate) {
        if (filter_var($candidate, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            $ip = $candidate;
            break;
        }
    }
    if ($ip === null) {
        http_response_code(500);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'code' => 500,
            'error' => 'No IPv6 address found'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    $ipType = 'IPv6';
} else {
    $ip = $rawIp;
    $ipType = filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) ? 'IPv6' : 'IPv4';
}

$country = getClientCountry($ip);
$timestamp = time();
$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
$requestUri = $_SERVER['REQUEST_URI'] ?? '';

if (DB_ENABLE) {
    try {
        $db = new Database();
        $db->logAccess($ip, $country, $mode, $userAgent, $requestUri);
    } catch (Exception $e) {
        error_log('Database error: ' . $e->getMessage());
    }
}

if ($mode === 'full') {
    header('Content-Type: application/json; charset=utf-8');
    
    $response = [
        'code' => 200,
        'ip' => $ip,
        'type' => $ipType,
        'location' => $country,
        'timestamp' => $timestamp,
        'datetime' => date('Y-m-d H:i:s', $timestamp),
        'mode' => $mode,
        'user_agent' => $userAgent
    ];
    
    echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
} else {
    header('Content-Type: text/plain; charset=utf-8');
    echo $ip;
}
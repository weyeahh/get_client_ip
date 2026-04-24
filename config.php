<?php
// 数据库配置
define('DB_ENABLE', true);               // 是否启用数据库记录 (true/false)
define('DB_TYPE', 'mysql');              // 数据库类型: mysql 或 sqlite

// MySQL 配置 (当 DB_TYPE=mysql 时有效)
define('DB_HOST', 'localhost');          // 数据库主机地址
define('DB_NAME', 'your_database_name'); // 数据库名称
define('DB_USER', 'your_database_user'); // 数据库用户名
define('DB_PASS', 'your_database_pass'); // 数据库密码
define('DB_CHARSET', 'utf8mb4');         // 数据库字符集

// SQLite 配置 (当 DB_TYPE=sqlite 时有效)
define('DB_SQLITE_PATH', __DIR__ . '/database.sqlite'); // SQLite 数据库文件路径

// 域名配置
define('DOMAIN_IPV4', 'ipv4.iusi.cn');   // 强制返回 IPv4 的域名
define('DOMAIN_IPV6', 'ipv6.iusi.cn');   // 强制返回 IPv6 的域名

// IP 获取配置
define('IP_HEADERS', [                   // 获取真实 IP 的请求头列表（按优先级顺序）
    'HTTP_ALI_REAL_CLIENT_IP',           // 阿里云 CDN
    'HTTP_CF_CONNECTING_IP',             // Cloudflare
    'HTTP_X_REAL_IP',                    // Nginx
    'HTTP_X_FORWARDED_FOR',              // 标准代理头
    'REMOTE_ADDR'                        // 直连 IP
]);

// 地区获取配置
define('COUNTRY_METHOD', 'header');      // 获取地区的方法: header 或 geoip

// Header 方式配置 (当 COUNTRY_METHOD=header 时有效)
define('COUNTRY_HEADERS', [              // 获取国家/地区的请求头列表（按优先级顺序）
    'HTTP_ALI_IP_COUNTRY',               // 阿里云 CDN
    'HTTP_CF_IPCOUNTRY',                 // Cloudflare
    'HTTP_X_COUNTRY_CODE'                // 其他 CDN
]);

// GeoIP 方式配置 (当 COUNTRY_METHOD=geoip 时有效)
define('GEOIP_DATABASE', __DIR__ . '/GeoLite2-Country.mmdb'); // GeoIP2 数据库文件路径

// 时区配置
date_default_timezone_set('Asia/Shanghai'); // PHP 时区设置
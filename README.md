# IP Address Service

一个轻量级的 PHP 服务，用于获取访问者的真实 IP 地址和地理位置信息。支持多种 CDN 和代理环境。

## 特性

- 🌐 支持多种 CDN（阿里云、Cloudflare、Nginx 等）
- 📍 多种地理位置获取方式（请求头 / GeoIP 库）
- 🔄 IPv4/IPv6 分离访问支持
- 💾 可选的访问日志记录（MySQL / SQLite）
- 📊 两种输出模式（纯文本 / JSON）
- 🧹 自动清理 30 天前的旧记录

## 快速开始

### 环境要求

- PHP 7.0+
- PDO 扩展
- （可选）MySQL 5.7+ 或 SQLite
- （可选）Composer（使用 GeoIP 时需要）

### 安装

1. 克隆项目
```bash
git clone https://github.com/your-username/ip-service.git
cd ip-service
```

2. 配置文件
```bash
cp config.php config.php
# 编辑 config.php 填入您的配置
```

3. （可选）安装 GeoIP 依赖
```bash
composer install
# 下载 GeoLite2-Country.mmdb 到项目根目录
```

4. （可选）导入数据库
```bash
# MySQL
mysql -u root -p < setup.sql

# SQLite 会自动创建
```

## 配置说明

### 数据库配置

```php
// 启用/禁用数据库记录
define('DB_ENABLE', true);

// 选择数据库类型
define('DB_TYPE', 'mysql');  // mysql 或 sqlite

// MySQL 配置
define('DB_HOST', 'localhost');
define('DB_NAME', 'your_database_name');
define('DB_USER', 'your_database_user');
define('DB_PASS', 'your_database_pass');

// SQLite 配置
define('DB_SQLITE_PATH', __DIR__ . '/database.sqlite');
```

### IP 获取配置

```php
define('IP_HEADERS', [
    'HTTP_ALI_REAL_CLIENT_IP',  // 阿里云 CDN
    'HTTP_CF_CONNECTING_IP',    // Cloudflare
    'HTTP_X_REAL_IP',           // Nginx
    'HTTP_X_FORWARDED_FOR',     // 标准代理
    'REMOTE_ADDR'               // 直连
]);
```

### 地理位置配置

```php
// 选择获取方式
define('COUNTRY_METHOD', 'header');  // header 或 geoip

// Header 方式
define('COUNTRY_HEADERS', [
    'HTTP_ALI_IP_COUNTRY',   // 阿里云
    'HTTP_CF_IPCOUNTRY',     // Cloudflare
    'HTTP_X_COUNTRY_CODE'    // 其他
]);

// GeoIP 方式
define('GEOIP_DATABASE', __DIR__ . '/GeoLite2-Country.mmdb');
```

### 域名配置

```php
// IPv4/IPv6 专用域名
define('DOMAIN_IPV4', 'ipv4.example.com');
define('DOMAIN_IPV6', 'ipv6.example.com');
```

## 使用方法

### Simple 模式（默认）

返回纯文本 IP 地址

```bash
curl https://your-domain.com/
# 输出: 1.2.3.4

curl https://your-domain.com/?mode=simple
# 输出: 1.2.3.4
```

### Full 模式

返回 JSON 格式详细信息

```bash
curl https://your-domain.com/?mode=full
```

```json
{
    "code": 200,
    "ip": "1.2.3.4",
    "type": "IPv4",
    "location": "cn",
    "timestamp": 1745510400,
    "datetime": "2026-04-24 22:00:00",
    "mode": "full",
    "user_agent": "curl/7.68.0"
}
```

### IPv4/IPv6 专用访问

```bash
# 仅返回 IPv4
curl https://ipv4.example.com/

# 仅返回 IPv6
curl https://ipv6.example.com/
```

## Web 服务器配置

### Nginx

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/ip-service;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### Apache

项目已包含 `.htaccess` 文件，确保启用 `mod_rewrite` 模块。

### 宝塔面板

1. 创建站点，选择 PHP 7.4+
2. 上传文件到站点根目录
3. 在数据库管理中创建数据库
4. 修改 `config.php` 填入数据库信息

## 数据库结构

表名：`access_logs`

| 字段 | 类型 | 说明 |
|------|------|------|
| id | INT/INTEGER | 主键 |
| ip | VARCHAR(45) | 访问者 IP |
| country | VARCHAR(2) | 国家代码（ISO 3166-1 Alpha-2）|
| mode | VARCHAR(10) | 请求模式 |
| user_agent | TEXT | 用户代理 |
| request_uri | VARCHAR(255) | 请求 URI |
| created_at | TIMESTAMP/DATETIME | 创建时间 |

## GeoIP 使用

1. 安装依赖
```bash
composer install
```

2. 下载数据库

访问 [MaxMind GeoLite2](https://dev.maxmind.com/geoip/geolite2-free-geolocation-data) 下载 `GeoLite2-Country.mmdb`

3. 启用 GeoIP
```php
define('COUNTRY_METHOD', 'geoip');
```

## 文件说明

```
.
├── index.php          # 主入口文件
├── config.php         # 配置文件
├── database.php       # 数据库操作类
├── geoip.php          # GeoIP 查询函数
├── setup.sql          # MySQL 初始化脚本
├── nginx.conf         # Nginx 配置示例
├── composer.json      # Composer 依赖
└── README.md          # 项目说明
```

## 许可证

MIT License

## 贡献

欢迎提交 Issue 和 Pull Request！

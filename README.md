# IP 获取服务

从阿里云 CDN 标头获取访问者真实 IP 地址的 PHP 服务。

## 功能

- 从 `ali-real-client-ip` 标头获取真实 IP
- 从 `ali-ip-country` 标头获取国家/地区代码
- 支持两种返回模式：simple（纯文本）和 full（JSON）
- 自动记录访问日志到 MySQL 数据库
- 自动清理 30 天前的旧记录

## 安装

1. 导入数据库：
```bash
mysql -u root -p < setup.sql
```

2. 配置数据库连接（编辑 `config.php`）：
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'getip');
define('DB_USER', 'root');
define('DB_PASS', 'your_password');
```

## 使用方法

### Simple 模式（默认）
```
GET /index.php
GET /index.php?mode=simple
```
返回：纯文本 IP 地址

### Full 模式
```
GET /index.php?mode=full
```
返回：JSON 格式的详细信息
```json
{
    "code": 200,
    "ip": "1.2.3.4",
    "location": "cn",
    "timestamp": 1713427446,
    "datetime": "2026-04-18 15:24:06",
    "mode": "full",
    "user_agent": "Mozilla/5.0..."
}
```

## 数据库结构

表名：`access_logs`

| 字段 | 类型 | 说明 |
|------|------|------|
| id | INT | 主键 |
| ip | VARCHAR(45) | 访问者 IP |
| country | VARCHAR(2) | 国家代码 |
| mode | VARCHAR(10) | 请求模式 |
| user_agent | TEXT | 用户代理 |
| request_uri | VARCHAR(255) | 请求 URI |
| created_at | TIMESTAMP | 创建时间 |

## 要求

- PHP 7.0+
- MySQL 5.7+
- PDO 扩展

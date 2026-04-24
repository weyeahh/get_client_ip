<?php
class Database {
    private $pdo;
    
    public function __construct() {
        if (!DB_ENABLE) {
            return;
        }
        
        try {
            if (DB_TYPE === 'sqlite') {
                $dsn = 'sqlite:' . DB_SQLITE_PATH;
                $this->pdo = new PDO($dsn, null, null, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]);
            } else {
                $dsn = sprintf(
                    'mysql:host=%s;dbname=%s;charset=%s',
                    DB_HOST,
                    DB_NAME,
                    DB_CHARSET
                );
                $this->pdo = new PDO($dsn, DB_USER, DB_PASS, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]);
            }
            
            $this->createTable();
            $this->cleanOldRecords();
        } catch (PDOException $e) {
            throw new Exception('Database connection failed: ' . $e->getMessage());
        }
    }
    
    private function createTable() {
        if (DB_TYPE === 'sqlite') {
            $sql = "CREATE TABLE IF NOT EXISTS access_logs (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                ip VARCHAR(45) NOT NULL,
                country VARCHAR(2) DEFAULT NULL,
                mode VARCHAR(10) DEFAULT 'simple',
                user_agent TEXT,
                request_uri VARCHAR(255),
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )";
            $this->pdo->exec($sql);
            $this->pdo->exec("CREATE INDEX IF NOT EXISTS idx_created_at ON access_logs(created_at)");
            $this->pdo->exec("CREATE INDEX IF NOT EXISTS idx_ip ON access_logs(ip)");
        } else {
            $sql = "CREATE TABLE IF NOT EXISTS access_logs (
                id INT AUTO_INCREMENT PRIMARY KEY,
                ip VARCHAR(45) NOT NULL,
                country VARCHAR(2) DEFAULT NULL,
                mode VARCHAR(10) DEFAULT 'simple',
                user_agent TEXT,
                request_uri VARCHAR(255),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_created_at (created_at),
                INDEX idx_ip (ip)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
            $this->pdo->exec($sql);
        }
    }
    
    public function logAccess($ip, $country, $mode, $userAgent, $requestUri) {
        if (!DB_ENABLE) {
            return;
        }
        
        $sql = "INSERT INTO access_logs (ip, country, mode, user_agent, request_uri) 
                VALUES (:ip, :country, :mode, :user_agent, :request_uri)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':ip' => $ip,
            ':country' => $country,
            ':mode' => $mode,
            ':user_agent' => $userAgent,
            ':request_uri' => $requestUri
        ]);
    }
    
    private function cleanOldRecords() {
        if (!DB_ENABLE) {
            return;
        }
        
        if (DB_TYPE === 'sqlite') {
            $sql = "DELETE FROM access_logs WHERE created_at < datetime('now', '-30 days')";
        } else {
            $sql = "DELETE FROM access_logs WHERE created_at < DATE_SUB(NOW(), INTERVAL 30 DAY)";
        }
        $this->pdo->exec($sql);
    }
}
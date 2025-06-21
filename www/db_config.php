<?php
// 本番環境用データベース設定
// Railway環境変数から取得

$db_config = [
    'host' => getenv('MYSQL_HOST') ?: getenv('DB_HOST') ?: 'localhost',
    'database' => getenv('MYSQL_DATABASE') ?: getenv('DB_NAME') ?: 'webapp',
    'username' => getenv('MYSQL_USER') ?: getenv('DB_USER') ?: 'root',
    'password' => getenv('MYSQL_PASSWORD') ?: getenv('DB_PASS') ?: '',
    'port' => getenv('MYSQL_PORT') ?: getenv('DB_PORT') ?: 3306
];

// PDO接続関数
function getDBConnection() {
    global $db_config;
    
    try {
        $dsn = "mysql:host={$db_config['host']};port={$db_config['port']};dbname={$db_config['database']};charset=utf8mb4";
        $pdo = new PDO(
            $dsn,
            $db_config['username'],
            $db_config['password'],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
            ]
        );
        return $pdo;
    } catch (PDOException $e) {
        return null;
    }
}

// データベース接続テスト
function testDBConnection() {
    $pdo = getDBConnection();
    if ($pdo) {
        try {
            $stmt = $pdo->query("SELECT 1");
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
    return false;
}
?>
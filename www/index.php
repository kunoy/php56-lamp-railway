<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LAMP Stack - CentOS 7 + Apache + PHP 5.5 + MariaDB</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .container { max-width: 800px; margin: 0 auto; }
        .status { padding: 10px; margin: 10px 0; border-radius: 5px; }
        .success { background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
        .error { background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
        .info { background-color: #d1ecf1; border: 1px solid #b8daff; color: #0c5460; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🐳 PHP 5.6 LAMP Stack</h1>
        <p>Classic PHP 5.6 + Apache + MySQL Compatible + Memcached + Mail Support</p>
        
        <h2>📋 システム情報</h2>
        <div class="info">
            <strong>PHP Version:</strong> <?php echo PHP_VERSION; ?><br>
            <strong>Server Software:</strong> <?php echo isset($_SERVER['SERVER_SOFTWARE']) ? $_SERVER['SERVER_SOFTWARE'] : 'Unknown'; ?><br>
            <strong>Document Root:</strong> <?php echo isset($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : 'Unknown'; ?><br>
            <strong>Server Time:</strong> <?php echo date('Y-m-d H:i:s'); ?>
        </div>

        <h2>🗄️ データベース接続テスト</h2>
        <?php
        include_once 'db_config.php';
        try {
            $pdo = getDBConnection();
            if (!$pdo) {
                throw new Exception('データベース接続を取得できませんでした');
            }
            echo '<div class="status success">✅ MariaDBへの接続に成功しました</div>';
            
            // テーブル作成とデータ挿入のテスト
            $pdo->exec("CREATE TABLE IF NOT EXISTS test_table (
                id INT AUTO_INCREMENT PRIMARY KEY,
                message VARCHAR(255),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )");
            
            $pdo->exec("INSERT INTO test_table (message) VALUES ('LAMP Stack Test - " . date('Y-m-d H:i:s') . "')");
            
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM test_table");
            $result = $stmt->fetch();
            echo '<div class="info">📊 test_tableレコード数: ' . $result['count'] . '</div>';
            
        } catch (Exception $e) {
            echo '<div class="status error">❌ データベース接続エラー: ' . $e->getMessage() . '</div>';
            echo '<div class="info">📝 ヒント: Railway環境では環境変数を確認してください → <a href="/env_check.php">環境変数チェック</a></div>';
        }
        ?>

        <h2>🚀 Memcached接続テスト</h2>
        <?php
        if (class_exists('Memcached')) {
            $memcached = new Memcached();
            $memcached->addServer(getenv('MEMCACHED_HOST'), 11211);
            
            $version = $memcached->getVersion();
            if ($version) {
                echo '<div class="status success">✅ Memcachedへの接続に成功しました</div>';
                echo '<div class="info">📦 Memcached Version: ' . implode(', ', $version) . '</div>';
                
                // キャッシュのテスト
                $test_key = 'lamp_test_' . time();
                $test_value = 'Hello from LAMP Stack!';
                
                if ($memcached->set($test_key, $test_value, 300)) {
                    $retrieved = $memcached->get($test_key);
                    if ($retrieved === $test_value) {
                        echo '<div class="info">💾 キャッシュの読み書きテスト: 成功</div>';
                    } else {
                        echo '<div class="status error">❌ キャッシュ読み取りエラー</div>';
                    }
                } else {
                    echo '<div class="status error">❌ キャッシュ書き込みエラー</div>';
                }
            } else {
                echo '<div class="status error">❌ Memcached接続エラー</div>';
            }
        } else {
            echo '<div class="status error">❌ Memcached拡張がインストールされていません</div>';
        }
        ?>

        <h2>🔧 PHP拡張モジュール</h2>
        <div class="info">
            <?php
            $extensions = ['pdo', 'pdo_mysql', 'mysqli', 'gd', 'curl', 'mbstring', 'xml', 'json', 'zip', 'mcrypt', 'memcached', 'opcache'];
            foreach ($extensions as $ext) {
                $status = extension_loaded($ext) ? '✅' : '❌';
                echo "<strong>{$status} {$ext}</strong><br>";
            }
            ?>
        </div>

        <h2>🔗 便利なリンク</h2>
        <ul>
            <li><a href="/info.php" target="_blank">📄 PHPinfo()</a></li>
            <li><a href="/test.php" target="_blank">🧪 簡易テストページ</a></li>
            <li><a href="/japanese_test.php" target="_blank">🇯🇵 日本語環境テスト</a></li>
            <li><a href="/mail_test.php" target="_blank">📧 メール送信テスト</a></li>
            <li><a href="/env_check.php" target="_blank">🔧 環境変数チェック</a></li>
        </ul>

        <h2>📝 環境の使い方</h2>
        <div class="info">
            <strong>Webサーバー:</strong> http://localhost:8080<br>
            <strong>HTTPS:</strong> https://localhost:8443<br>
            <strong>MySQL接続:</strong> localhost:3306 (webuser/webpassword)<br>
            <strong>Memcached:</strong> localhost:11211<br>
            <strong>ファイル配置:</strong> ./www/ ディレクトリにファイルを配置<br>
            <strong>ログ確認:</strong> ./logs/apache/ ディレクトリ
        </div>
    </div>
</body>
</html>
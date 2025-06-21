<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>環境変数チェック - PHP 5.6 LAMP</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background-color: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; }
        .env-item { padding: 10px; margin: 5px 0; border-radius: 4px; }
        .set { background-color: #d4edda; border: 1px solid #c3e6cb; }
        .not-set { background-color: #f8d7da; border: 1px solid #f5c6cb; }
        .info { background-color: #d1ecf1; border: 1px solid #b8daff; padding: 15px; margin: 20px 0; border-radius: 5px; }
        .back-link { display: inline-block; margin-top: 20px; padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 環境変数チェック</h1>
        <p>Railway本番環境での環境変数設定状況を確認します。</p>

        <h2>📊 データベース設定</h2>
        <?php
        $db_vars = [
            'MYSQL_HOST' => 'データベースホスト',
            'MYSQL_DATABASE' => 'データベース名',
            'MYSQL_USER' => 'データベースユーザー',
            'MYSQL_PASSWORD' => 'データベースパスワード',
            'MYSQL_PORT' => 'データベースポート'
        ];

        foreach ($db_vars as $var => $description) {
            $value = getenv($var);
            $class = $value ? 'set' : 'not-set';
            $status = $value ? '✅ 設定済み' : '❌ 未設定';
            
            echo "<div class='env-item $class'>";
            echo "<strong>$var</strong> ($description): $status";
            if ($value && !in_array($var, ['MYSQL_PASSWORD'])) {
                echo " - <code>" . htmlspecialchars($value) . "</code>";
            } elseif ($value && $var === 'MYSQL_PASSWORD') {
                echo " - <code>***********</code>";
            }
            echo "</div>";
        }
        ?>

        <h2>🌐 システム環境</h2>
        <?php
        $system_vars = [
            'PORT' => 'アプリケーションポート',
            'RAILWAY_ENVIRONMENT' => 'Railway環境',
            'RAILWAY_SERVICE_NAME' => 'サービス名'
        ];

        foreach ($system_vars as $var => $description) {
            $value = getenv($var);
            $class = $value ? 'set' : 'not-set';
            $status = $value ? '✅ 設定済み' : '❌ 未設定';
            
            echo "<div class='env-item $class'>";
            echo "<strong>$var</strong> ($description): $status";
            if ($value) {
                echo " - <code>" . htmlspecialchars($value) . "</code>";
            }
            echo "</div>";
        }
        ?>

        <h2>🔄 データベース接続テスト</h2>
        <?php
        include_once 'db_config.php';
        
        if (testDBConnection()) {
            echo '<div class="env-item set">✅ データベース接続: 成功</div>';
        } else {
            echo '<div class="env-item not-set">❌ データベース接続: 失敗</div>';
            echo '<div class="info">';
            echo '<strong>接続に失敗した場合の対処法:</strong><br>';
            echo '1. Railway管理画面でMySQL変数を確認<br>';
            echo '2. 環境変数がService → Variablesに正しく設定されているか確認<br>';
            echo '3. データベースサービスが起動しているか確認';
            echo '</div>';
        }
        ?>

        <h2>📋 システム情報</h2>
        <div class="info">
            <strong>PHP Version:</strong> <?php echo PHP_VERSION; ?><br>
            <strong>Server Software:</strong> <?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?><br>
            <strong>Document Root:</strong> <?php echo $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown'; ?><br>
            <strong>Server Name:</strong> <?php echo $_SERVER['SERVER_NAME'] ?? 'Unknown'; ?><br>
            <strong>Current Time:</strong> <?php echo date('Y-m-d H:i:s T'); ?>
        </div>

        <a href="/" class="back-link">← メインページに戻る</a>
    </div>
</body>
</html>
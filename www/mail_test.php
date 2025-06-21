<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP 5.6 メール送信テスト</title>
    <style>
        body { font-family: 'Hiragino Sans', 'ヒラギノ角ゴシック', sans-serif; margin: 40px; }
        .container { max-width: 800px; margin: 0 auto; }
        .status { padding: 10px; margin: 10px 0; border-radius: 5px; }
        .success { background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
        .error { background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
        .info { background-color: #d1ecf1; border: 1px solid #b8daff; color: #0c5460; }
        .form-group { margin: 15px 0; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group textarea { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        .form-group textarea { height: 100px; resize: vertical; }
        .btn { background-color: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        .btn:hover { background-color: #0056b3; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📧 PHP 5.6 メール送信テスト</h1>
        <p>Postfix + PHP mail()関数を使用したメール送信のテストページです。</p>

        <h2>📋 メール設定確認</h2>
        <div class="info">
            <strong>sendmail_path:</strong> <?php echo ini_get('sendmail_path') ?: '設定なし'; ?><br>
            <strong>SMTP:</strong> <?php echo ini_get('SMTP') ?: '設定なし'; ?><br>
            <strong>smtp_port:</strong> <?php echo ini_get('smtp_port') ?: '設定なし'; ?><br>
            <strong>mail.add_x_header:</strong> <?php echo ini_get('mail.add_x_header') ? 'On' : 'Off'; ?>
        </div>

        <?php
        if ($_POST && isset($_POST['send_mail'])) {
            $to = $_POST['to'] ?? '';
            $subject = $_POST['subject'] ?? '';
            $message = $_POST['message'] ?? '';
            $from = $_POST['from'] ?? 'noreply@localhost.localdomain';
            
            if (empty($to) || empty($subject) || empty($message)) {
                echo '<div class="status error">❌ すべてのフィールドを入力してください。</div>';
            } else {
                // メールヘッダーを設定
                $headers = array();
                $headers[] = 'From: ' . $from;
                $headers[] = 'Reply-To: ' . $from;
                $headers[] = 'Content-Type: text/plain; charset=UTF-8';
                $headers[] = 'Content-Transfer-Encoding: 8bit';
                $headers[] = 'X-Mailer: PHP/' . phpversion();
                
                $header_string = implode("\r\n", $headers);
                
                // 日本語件名のエンコーディング
                $encoded_subject = mb_encode_mimeheader($subject, 'UTF-8');
                
                // メール送信
                if (mail($to, $encoded_subject, $message, $header_string)) {
                    echo '<div class="status success">✅ メールが正常に送信されました！</div>';
                    echo '<div class="info">';
                    echo '<strong>送信先:</strong> ' . htmlspecialchars($to) . '<br>';
                    echo '<strong>件名:</strong> ' . htmlspecialchars($subject) . '<br>';
                    echo '<strong>送信者:</strong> ' . htmlspecialchars($from) . '<br>';
                    echo '<strong>送信時刻:</strong> ' . date('Y-m-d H:i:s') . '<br>';
                    echo '</div>';
                } else {
                    echo '<div class="status error">❌ メールの送信に失敗しました。</div>';
                    $last_error = error_get_last();
                    if ($last_error) {
                        echo '<div class="info">エラー詳細: ' . htmlspecialchars($last_error['message']) . '</div>';
                    }
                }
            }
        }
        ?>

        <h2>📤 メール送信テスト</h2>
        <form method="post">
            <div class="form-group">
                <label for="to">送信先メールアドレス:</label>
                <input type="email" id="to" name="to" value="<?php echo htmlspecialchars($_POST['to'] ?? 'test@localhost.localdomain'); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="from">送信者メールアドレス:</label>
                <input type="email" id="from" name="from" value="<?php echo htmlspecialchars($_POST['from'] ?? 'noreply@localhost.localdomain'); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="subject">件名:</label>
                <input type="text" id="subject" name="subject" value="<?php echo htmlspecialchars($_POST['subject'] ?? 'PHP 5.6 LAMPスタックからのテストメール'); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="message">メッセージ:</label>
                <textarea id="message" name="message" required><?php echo htmlspecialchars($_POST['message'] ?? "こんにちは！\n\nこれはPHP 5.6 LAMPスタック環境からのテストメールです。\n\n環境情報:\n- PHP Version: " . PHP_VERSION . "\n- Server: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "\n- 送信時刻: " . date('Y-m-d H:i:s') . "\n\nPostfixとmail()関数が正常に動作しています。\n\n---\nPHP 5.6 LAMP Stack\nDevelopment Environment"); ?></textarea>
            </div>
            
            <button type="submit" name="send_mail" class="btn">📧 メール送信</button>
        </form>

        <h2>🔧 Postfixサービス状態確認</h2>
        <div class="info">
            <?php
            // Postfixプロセス確認
            $postfix_status = shell_exec('ps aux | grep postfix | grep -v grep') ?: '';
            if (empty($postfix_status)) {
                echo '❌ Postfixプロセスが見つかりません<br>';
            } else {
                echo '✅ Postfixプロセスが実行中です<br>';
                echo '<pre style="font-size: 12px; margin-top: 10px;">' . htmlspecialchars($postfix_status) . '</pre>';
            }
            
            // ポート25の確認
            $port_check = shell_exec('netstat -ln | grep :25') ?: '';
            if (empty($port_check)) {
                echo '❌ ポート25がリスニングしていません<br>';
            } else {
                echo '✅ ポート25でリスニング中です<br>';
                echo '<pre style="font-size: 12px;">' . htmlspecialchars($port_check) . '</pre>';
            }
            ?>
        </div>

        <h2>📝 メールログ確認</h2>
        <div class="info">
            <?php
            $maillog = shell_exec('tail -n 10 /var/log/mail.log 2>/dev/null') ?: shell_exec('tail -n 10 /var/log/syslog 2>/dev/null | grep postfix') ?: 'ログファイルが見つかりません';
            echo '<pre style="font-size: 12px; max-height: 200px; overflow-y: auto;">' . htmlspecialchars($maillog) . '</pre>';
            ?>
        </div>

        <h2>🔗 関連リンク</h2>
        <ul>
            <li><a href="/" target="_blank">📊 メインダッシュボード</a></li>
            <li><a href="/test.php" target="_blank">🧪 システムテスト</a></li>
            <li><a href="/japanese_test.php" target="_blank">🇯🇵 日本語環境テスト</a></li>
            <li><a href="/info.php" target="_blank">📄 PHPinfo()</a></li>
        </ul>
    </div>
</body>
</html>
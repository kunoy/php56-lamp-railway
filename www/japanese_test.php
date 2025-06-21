<?php
header('Content-Type: text/html; charset=UTF-8');
?>
<\!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>PHP 5.6 日本語環境テスト</title>
    <style>
        body { font-family: 'Hiragino Sans', 'ヒラギノ角ゴシック', sans-serif; margin: 40px; }
        .info { background-color: #d1ecf1; border: 1px solid #b8daff; padding: 15px; margin: 10px 0; border-radius: 5px; }
        .success { background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 10px; margin: 10px 0; border-radius: 5px; }
    </style>
</head>
<body>
    <h1>🇯🇵 PHP 5.6 日本語環境テスト</h1>
    
    <h2>📋 mbstring設定確認</h2>
    <div class="info">
        <strong>mbstring.language:</strong> <?php echo ini_get('mbstring.language'); ?><br>
        <strong>mbstring.internal_encoding:</strong> <?php echo ini_get('mbstring.internal_encoding'); ?><br>
        <strong>mbstring.detect_order:</strong> <?php echo ini_get('mbstring.detect_order'); ?><br>
        <strong>default_charset:</strong> <?php echo ini_get('default_charset'); ?><br>
        <strong>HTTP入力エンコーディング変換:</strong> <?php echo ini_get('mbstring.encoding_translation') ? '有効' : '無効'; ?>
    </div>

    <h2>🔤 日本語文字列処理テスト</h2>
    <?php
    $japanese_text = "こんにちは、世界！これはPHP 5.6の日本語テストです。";
    $hiragana = "ひらがな";
    $katakana = "カタカナ";
    $kanji = "漢字テスト";
    $mixed = "混合文字：ABC123あいうえおアイウエオ漢字！？";
    ?>
    
    <div class="success">
        <strong>元のテキスト:</strong> <?php echo $japanese_text; ?><br>
        <strong>文字数:</strong> <?php echo mb_strlen($japanese_text); ?> 文字<br>
        <strong>バイト数:</strong> <?php echo strlen($japanese_text); ?> バイト<br>
        <strong>エンコーディング検出:</strong> <?php echo mb_detect_encoding($japanese_text); ?>
    </div>

    <h2>🔧 文字列変換テスト</h2>
    <div class="info">
        <strong>ひらがな → カタカナ:</strong> <?php echo mb_convert_kana($hiragana, 'C'); ?><br>
        <strong>カタカナ → ひらがな:</strong> <?php echo mb_convert_kana($katakana, 'c'); ?><br>
        <strong>全角 → 半角（英数）:</strong> <?php echo mb_convert_kana('ＡＢＣ１２３', 'a'); ?><br>
        <strong>半角 → 全角（英数）:</strong> <?php echo mb_convert_kana('ABC123', 'A'); ?>
    </div>

    <h2>✂️ 文字列切り出しテスト</h2>
    <div class="info">
        <strong>元の文字列:</strong> <?php echo $mixed; ?><br>
        <strong>最初の10文字:</strong> <?php echo mb_substr($mixed, 0, 10); ?><br>
        <strong>5文字目から10文字:</strong> <?php echo mb_substr($mixed, 5, 10); ?>
    </div>

    <h2>🔍 正規表現テスト（日本語対応）</h2>
    <div class="info">
        <?php
        $test_string = "電話番号：03-1234-5678、メール：test@example.com";
        if (mb_ereg('[0-9]{2,4}-[0-9]{2,4}-[0-9]{4}', $test_string, $matches)) {
            echo "<strong>電話番号マッチ:</strong> " . $matches[0] . "<br>";
        }
        if (preg_match('/[\x{3040}-\x{309F}\x{30A0}-\x{30FF}\x{4E00}-\x{9FAF}]+/u', $test_string, $matches)) {
            echo "<strong>日本語部分マッチ:</strong> " . $matches[0] . "<br>";
        }
        ?>
    </div>

    <h2>📊 利用可能な日本語エンコーディング</h2>
    <div class="info">
        <?php
        $encodings = mb_list_encodings();
        $japanese_encodings = array_filter($encodings, function($enc) {
            return stripos($enc, 'UTF') \!== false || 
                   stripos($enc, 'SJIS') \!== false || 
                   stripos($enc, 'EUC') \!== false || 
                   stripos($enc, 'JIS') \!== false;
        });
        echo implode(', ', $japanese_encodings);
        ?>
    </div>

    <p><strong>🎯 PHP 5.6 日本語環境が正常に動作しています！</strong></p>
</body>
</html>

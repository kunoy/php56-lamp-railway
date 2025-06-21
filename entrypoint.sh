#!/bin/bash

# PHP 5.6専用の自動起動スクリプト

echo "=== PHP 5.6 LAMP Stack Starting ==="
echo "TRUE PHP 5.6 + Apache + MariaDB + Memcached + Postfix + Certbot"

# Memcachedサービス開始
echo "Starting Memcached service..."
service memcached start

# Postfixサービス開始
echo "Starting Postfix service..."
service postfix start

# Apache設定テスト
echo "Testing Apache configuration..."
apache2ctl configtest

if [ $? -eq 0 ]; then
    echo "✅ Apache configuration is OK"
else
    echo "❌ Apache configuration has errors"
    exit 1
fi

echo ""
echo "=== PHP 5.6 Version Information ==="
php -v

echo ""
echo "=== PHP 5.6 LAMP Stack Ready ==="
echo "🌐 Web Server: http://localhost:8080"
echo "🔒 HTTPS: https://localhost:8443"
echo "🧪 Test Page: http://localhost:8080/test.php"
echo "📄 PHP Info: http://localhost:8080/info.php"
echo "📊 Dashboard: http://localhost:8080/"
echo ""
echo "Database Info:"
echo "  Host: mariadb:3306"
echo "  Database: webapp"
echo "  User: webuser"
echo "  Password: webpassword"
echo ""
echo "Memcached: memcached:11211"
echo "Mail Server: Postfix SMTP on localhost:25"
echo ""
echo "🎯 PHP Version: $(php -r 'echo PHP_VERSION;')"
echo "🎯 PHP SAPI: $(php -r 'echo PHP_SAPI;')"

# Apache をフォアグラウンドで実行
echo "Starting Apache with PHP 5.6 in foreground mode..."
exec apache2-foreground
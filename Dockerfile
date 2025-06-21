FROM php:5.6-apache

# タイムゾーン設定
ENV TZ=Asia/Tokyo

# アーカイブリポジトリに変更（Debian Stretch対応）
RUN echo "deb http://archive.debian.org/debian stretch main" > /etc/apt/sources.list \
    && echo "deb http://archive.debian.org/debian-security stretch/updates main" >> /etc/apt/sources.list \
    && echo "Acquire::Check-Valid-Until \"false\";" > /etc/apt/apt.conf.d/10no--check-valid-until \
    && echo "APT::Get::AllowUnauthenticated \"true\";" >> /etc/apt/apt.conf.d/10no--check-valid-until

# Postfix事前設定（対話プロンプトを回避）
RUN echo "postfix postfix/mailname string localhost.localdomain" | debconf-set-selections \
    && echo "postfix postfix/main_mailer_type string 'Local only'" | debconf-set-selections

# 必要なパッケージのインストール
RUN apt-get update && apt-get install -y --allow-unauthenticated \
    libmcrypt-dev \
    libmemcached-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libxml2-dev \
    libcurl4-openssl-dev \
    libzip-dev \
    zlib1g-dev \
    mysql-client \
    memcached \
    wget \
    curl \
    openssl \
    postfix \
    mailutils \
    && rm -rf /var/lib/apt/lists/*

# PHP拡張のインストール
RUN docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install gd \
    && docker-php-ext-install mysqli pdo pdo_mysql \
    && docker-php-ext-install curl \
    && docker-php-ext-install mbstring \
    && docker-php-ext-install mcrypt \
    && docker-php-ext-install zip \
    && docker-php-ext-install opcache

# Memcached拡張のインストール
RUN pecl install memcached-2.2.0 \
    && docker-php-ext-enable memcached

# PHPの設定
RUN echo "date.timezone = Asia/Tokyo" >> /usr/local/etc/php/php.ini \
    && echo "upload_max_filesize = 100M" >> /usr/local/etc/php/php.ini \
    && echo "post_max_size = 100M" >> /usr/local/etc/php/php.ini \
    && echo "max_execution_time = 300" >> /usr/local/etc/php/php.ini \
    && echo "memory_limit = 256M" >> /usr/local/etc/php/php.ini \
    && echo "sendmail_path = /usr/sbin/sendmail -t -i" >> /usr/local/etc/php/php.ini \
    && echo "SMTP = localhost" >> /usr/local/etc/php/php.ini \
    && echo "smtp_port = 25" >> /usr/local/etc/php/php.ini

# Apacheモジュール有効化
RUN a2enmod rewrite ssl headers

# Apache設定
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf \
    && echo "ServerTokens Prod" >> /etc/apache2/apache2.conf \
    && echo "ServerSignature Off" >> /etc/apache2/apache2.conf

# Postfix設定
COPY config/postfix/main.cf /etc/postfix/main.cf
COPY config/postfix/master.cf /etc/postfix/master.cf
RUN newaliases \
    && postmap /etc/postfix/main.cf

# SSL証明書生成
RUN mkdir -p /etc/ssl/certs \
    && openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
        -keyout /etc/ssl/certs/server.key \
        -out /etc/ssl/certs/server.crt \
        -subj "/C=JP/ST=Tokyo/L=Tokyo/O=Development/CN=localhost"

# DocumentRootの設定
WORKDIR /var/www/html

# デフォルトのPHPテストページ作成
RUN echo '<?php phpinfo(); ?>' > /var/www/html/info.php \
    && echo '<?php' > /var/www/html/test.php \
    && echo 'echo "<h1>🎯 TRUE PHP 5.6 LAMP Stack Test</h1>";' >> /var/www/html/test.php \
    && echo 'echo "PHP Version: " . PHP_VERSION . "<br>";' >> /var/www/html/test.php \
    && echo 'echo "PHP Major Version: " . PHP_MAJOR_VERSION . "<br>";' >> /var/www/html/test.php \
    && echo 'echo "OS: " . php_uname() . "<br>";' >> /var/www/html/test.php \
    && echo 'echo "Server Software: " . $_SERVER["SERVER_SOFTWARE"] . "<br>";' >> /var/www/html/test.php \
    && echo 'if (class_exists("Memcached")) {' >> /var/www/html/test.php \
    && echo '    echo "<br><strong>Memcached Test:</strong><br>";' >> /var/www/html/test.php \
    && echo '    $memcached = new Memcached();' >> /var/www/html/test.php \
    && echo '    $memcached->addServer("memcached", 11211);' >> /var/www/html/test.php \
    && echo '    $version = $memcached->getVersion();' >> /var/www/html/test.php \
    && echo '    if ($version) {' >> /var/www/html/test.php \
    && echo '        echo "✅ Memcached connection: OK<br>";' >> /var/www/html/test.php \
    && echo '        echo "Memcached version: " . implode(", ", $version) . "<br>";' >> /var/www/html/test.php \
    && echo '    } else {' >> /var/www/html/test.php \
    && echo '        echo "❌ Memcached connection: Failed<br>";' >> /var/www/html/test.php \
    && echo '    }' >> /var/www/html/test.php \
    && echo '} else {' >> /var/www/html/test.php \
    && echo '    echo "❌ Memcached extension not available<br>";' >> /var/www/html/test.php \
    && echo '}' >> /var/www/html/test.php \
    && echo 'echo "<br><strong>Database Test:</strong><br>";' >> /var/www/html/test.php \
    && echo 'try {' >> /var/www/html/test.php \
    && echo '    $pdo = new PDO("mysql:host=mariadb;dbname=webapp", "webuser", "webpassword");' >> /var/www/html/test.php \
    && echo '    if ($pdo) echo "✅ Database connection: OK<br>";' >> /var/www/html/test.php \
    && echo '} catch (Exception $e) {' >> /var/www/html/test.php \
    && echo '    echo "❌ Database connection: Failed - " . $e->getMessage() . "<br>";' >> /var/www/html/test.php \
    && echo '}' >> /var/www/html/test.php \
    && echo 'echo "<br><strong>PHP Extensions:</strong><br>";' >> /var/www/html/test.php \
    && echo '$extensions = ["mysql", "mysqli", "pdo", "pdo_mysql", "gd", "mbstring", "curl", "mcrypt", "opcache", "memcached"];' >> /var/www/html/test.php \
    && echo 'foreach ($extensions as $ext) {' >> /var/www/html/test.php \
    && echo '    $status = extension_loaded($ext) ? "✅" : "❌";' >> /var/www/html/test.php \
    && echo '    echo "{$status} {$ext}<br>";' >> /var/www/html/test.php \
    && echo '}' >> /var/www/html/test.php \
    && echo 'echo "<br><strong>🎯 This is TRUE PHP 5.6!</strong><br>";' >> /var/www/html/test.php \
    && echo '?>' >> /var/www/html/test.php

# 起動スクリプト
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# ポート80と443を公開
EXPOSE 80 443

CMD ["/entrypoint.sh"]
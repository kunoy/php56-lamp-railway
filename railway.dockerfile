FROM php:5.6-apache

# タイムゾーン設定
ENV TZ=Asia/Tokyo

# Railway用の環境変数設定
ENV PORT=80
ENV APACHE_DOCUMENT_ROOT=/var/www/html

# アーカイブリポジトリに変更（Debian Stretch対応）
RUN echo "deb http://archive.debian.org/debian stretch main" > /etc/apt/sources.list \
    && echo "deb http://archive.debian.org/debian-security stretch/updates main" >> /etc/apt/sources.list \
    && echo "Acquire::Check-Valid-Until \"false\";" > /etc/apt/apt.conf.d/10no--check-valid-until \
    && echo "APT::Get::AllowUnauthenticated \"true\";" >> /etc/apt/apt.conf.d/10no--check-valid-until

# 必要なパッケージのインストール（軽量化）
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
    curl \
    openssl \
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
    && echo "default_charset = \"UTF-8\"" >> /usr/local/etc/php/php.ini \
    && echo "mbstring.language = Japanese" >> /usr/local/etc/php/php.ini \
    && echo "mbstring.internal_encoding = UTF-8" >> /usr/local/etc/php/php.ini \
    && echo "mbstring.http_input = auto" >> /usr/local/etc/php/php.ini \
    && echo "mbstring.http_output = UTF-8" >> /usr/local/etc/php/php.ini \
    && echo "mbstring.encoding_translation = On" >> /usr/local/etc/php/php.ini \
    && echo "mbstring.detect_order = auto" >> /usr/local/etc/php/php.ini

# Apacheモジュール有効化
RUN a2enmod rewrite ssl headers

# Apache設定（Railway用）
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf \
    && echo "ServerTokens Prod" >> /etc/apache2/apache2.conf \
    && echo "ServerSignature Off" >> /etc/apache2/apache2.conf

# Railway用のApache設定
RUN sed -i 's/Listen 80/Listen ${PORT}/' /etc/apache2/ports.conf \
    && sed -i 's/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/' /etc/apache2/sites-available/000-default.conf

# SSL証明書生成（開発用）
RUN mkdir -p /etc/ssl/certs \
    && openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
        -keyout /etc/ssl/certs/server.key \
        -out /etc/ssl/certs/server.crt \
        -subj "/C=JP/ST=Tokyo/L=Tokyo/O=Development/CN=localhost"

# Sendmailシミュレーター（Railway用）
RUN echo '#!/bin/bash\n\
LOG_FILE="/tmp/mail.log"\n\
TIMESTAMP=$(date "+%Y-%m-%d %H:%M:%S")\n\
echo "[$TIMESTAMP] Simulated email send:" >> $LOG_FILE\n\
while IFS= read -r line; do\n\
    echo "  $line" >> $LOG_FILE\n\
done\n\
echo "[$TIMESTAMP] Email logged successfully" >> $LOG_FILE\n\
echo "  ---" >> $LOG_FILE\n\
exit 0' > /usr/local/bin/sendmail \
    && chmod +x /usr/local/bin/sendmail

# PHP sendmail設定
RUN echo "sendmail_path = /usr/local/bin/sendmail -t -i" >> /usr/local/etc/php/php.ini

# アプリケーションファイルをコピー
COPY www/ /var/www/html/

# 権限設定
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# ヘルスチェック用エンドポイント
RUN echo '<?php echo "OK"; ?>' > /var/www/html/health.php

# ポート公開
EXPOSE ${PORT}

# Apache をフォアグラウンドで実行
CMD ["apache2-foreground"]
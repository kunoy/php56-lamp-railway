# PHP 5.6 LAMP Stack

Classic PHP 5.6 LAMP stack environment with modern deployment capabilities.

## 🚀 Features

- **PHP 5.6.40** - Legacy PHP support
- **Apache 2.4** - Web server
- **MySQL Compatible** - Database support (MariaDB/MySQL)
- **Memcached** - Caching layer
- **Japanese Language Support** - Full UTF-8 and mbstring support
- **Mail Functionality** - PHP mail() function support
- **Docker Ready** - Container deployment ready

## 🛠️ Local Development

### Using Docker Compose

```bash
# Clone the repository
git clone <your-repo-url>
cd markitdown

# Start the stack
docker-compose up -d

# Access the application
open http://localhost:8080
```

### Available Services

- **Web Application**: http://localhost:8080
- **PHP Info**: http://localhost:8080/info.php
- **Japanese Test**: http://localhost:8080/japanese_test.php
- **Mail Test**: http://localhost:8080/mail_test.php
- **Database**: localhost:3306 (webuser/webpassword)
- **Memcached**: localhost:11211

## 🌐 Production Deployment

### Railway Deployment

1. Fork this repository
2. Connect to Railway
3. Add MySQL database service
4. Set environment variables:
   - `MYSQL_HOST`
   - `MYSQL_DATABASE`
   - `MYSQL_USER`
   - `MYSQL_PASSWORD`
   - `MYSQL_PORT`

### Other Platforms

The application supports deployment on:
- Railway (recommended for low cost)
- Render
- DigitalOcean App Platform
- AWS ECS/Fargate
- Google Cloud Run

## 📋 Environment Variables

### Database Configuration
```
MYSQL_HOST=your-database-host
MYSQL_DATABASE=your-database-name
MYSQL_USER=your-database-user
MYSQL_PASSWORD=your-database-password
MYSQL_PORT=3306
```

## 🧪 Testing

- **System Test**: `/test.php`
- **Japanese Language**: `/japanese_test.php`
- **Mail Function**: `/mail_test.php`
- **Health Check**: `/health.php`

## 📦 Docker Images

### Development
```bash
docker-compose up -d
```

### Production (Railway)
```bash
docker build -f railway.dockerfile -t php56-lamp .
```

## 🔧 Configuration

### PHP Extensions Included
- mysqli, pdo, pdo_mysql
- gd, curl, mbstring
- mcrypt, zip, opcache
- memcached

### Japanese Language Support
- mbstring configured for Japanese
- UTF-8 default charset
- Timezone: Asia/Tokyo

## 📝 License

This project is intended for educational and development purposes.

## 🤝 Support

For issues and questions, please create an issue in this repository.
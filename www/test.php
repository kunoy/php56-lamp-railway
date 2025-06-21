<?php
echo "<h1>🎯 TRUE PHP 5.6 LAMP Stack Test</h1>";
echo "PHP Version: " . PHP_VERSION . "<br>";
echo "PHP Major Version: " . PHP_MAJOR_VERSION . "<br>";
echo "OS: " . php_uname() . "<br>";
echo "Server Software: " . $_SERVER["SERVER_SOFTWARE"] . "<br>";
if (class_exists("Memcached")) {
    echo "<br><strong>Memcached Test:</strong><br>";
    $memcached = new Memcached();
    $memcached->addServer("memcached", 11211);
    $version = $memcached->getVersion();
    if ($version) {
        echo "✅ Memcached connection: OK<br>";
        echo "Memcached version: " . implode(", ", $version) . "<br>";
    } else {
        echo "❌ Memcached connection: Failed<br>";
    }
} else {
    echo "❌ Memcached extension not available<br>";
}
echo "<br><strong>Database Test:</strong><br>";
try {
    $pdo = new PDO("mysql:host=mariadb;dbname=webapp", "webuser", "webpassword");
    if ($pdo) echo "✅ Database connection: OK<br>";
} catch (Exception $e) {
    echo "❌ Database connection: Failed - " . $e->getMessage() . "<br>";
}
echo "<br><strong>PHP Extensions:</strong><br>";
$extensions = ["mysql", "mysqli", "pdo", "pdo_mysql", "gd", "mbstring", "curl", "mcrypt", "opcache", "memcached"];
foreach ($extensions as $ext) {
    $status = extension_loaded($ext) ? "✅" : "❌";
    echo "{$status} {$ext}<br>";
}
echo "<br><strong>🎯 This is TRUE PHP 5.6\!</strong><br>";
?>

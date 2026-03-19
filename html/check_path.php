<?php
echo "<h2>Проверка путей из папки html</h2>";
echo "Текущая папка: " . __DIR__ . "<br>";
echo "Родительская папка: " . dirname(__DIR__) . "<br>";

$files = [
    'database.php' => dirname(__DIR__) . '/config/database.php',
    'process_order.php' => dirname(__DIR__) . '/php/process_order.php',
    'log file' => dirname(__DIR__) . '/order_debug.log'
];

foreach ($files as $name => $path) {
    echo "$name: ";
    if (file_exists($path)) {
        echo " найден ($path)<br>";
    } else {
        echo "НЕ найден ($path)<br>";
    }
}

echo "<h3>Проверка доступности файлов через HTTP</h3>";
$http_files = [
    '/php/process_order.php',
    '../php/process_order.php',
    'process_order.php'
];

foreach ($http_files as $file) {
    echo "Проверка $file... ";
    $url = $file;
    $headers = @get_headers($url);
    if ($headers && strpos($headers[0], '200')) {
        echo " доступен<br>";
    } else {
        echo " НЕ доступен<br>";
    }
}
?>
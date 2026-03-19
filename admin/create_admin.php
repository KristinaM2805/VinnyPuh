<?php
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

if (!$db) {
    die("Ошибка подключения к БД");
}

$password = "admin123";
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

echo "<h2>Создание администратора</h2>";
echo "Хэш пароля 'admin123':<br>";
echo "<div style='background: #eee; padding: 10px; word-wrap: break-word;'>" . $hashed_password . "</div><br>";

try {
    $tableExists = $db->query("SHOW TABLES LIKE 'users'")->rowCount();
    
    if ($tableExists == 0) {
        echo " Таблица 'users' не существует! Сначала создайте таблицы.<br>";
        echo "<a href='http://localhost/phpMyAdmin/' target='_blank'>Открыть phpMyAdmin</a>";
        exit;
    }

    $db->exec("DELETE FROM users WHERE username = 'admin'");
    echo "Старый администратор удален<br>";

    $query = "INSERT INTO users (username, password, role) VALUES (:username, :password, 'admin')";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $hashed_password);

    $username = 'admin';

    if ($stmt->execute()) {
        echo "Администратор создан!<br><br>";
        echo "<strong>Логин:</strong> admin<br>";
        echo "<strong>Пароль:</strong> admin123<br><br>";
        
        $check = $db->query("SELECT username FROM users WHERE username = 'admin'")->fetchColumn();
        echo "Проверка: пользователь '$check' существует.<br><br>";
        

        $stmt = $db->prepare("SELECT password FROM users WHERE username = 'admin'");
        $stmt->execute();
        $db_password = $stmt->fetchColumn();
        
        if (password_verify('admin123', $db_password)) {
            echo "Пароль проверен успешно!<br><br>";
            echo "<a href='login.php'>Перейти к входу</a>";
        } else {
            echo "Пароль не совпадает!";
        }
        
    } else {
        echo " Ошибка при создании администратора";
    }
    
} catch (Exception $e) {
    echo " Ошибка: " . $e->getMessage() . "<br>";
    echo "Проверьте, созданы ли таблицы в базе данных.";
}
?>
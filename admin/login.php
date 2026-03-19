<?php
session_start();
require_once '../config/database.php';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $database = new Database();
    $db = $database->getConnection();
    
    if (!$db) {
        $error = "Ошибка подключения к базе данных";
    } else {
        $username = $_POST['username'];
        $password = $_POST['password'];
        
        $query = "SELECT id, username, password, role FROM users WHERE username = :username";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':username', $username);
        
        if ($stmt->execute()) {
            if ($stmt->rowCount() == 1) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                if (password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role'] = $user['role'];
                    header("Location: index.php");
                    exit;
                }
            }
        }
        $error = "Неверное имя пользователя или пароль";
    }
}
?>













<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Вход в админку</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            background: #f5f5f5; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            height: 100vh; 
            margin: 0;
        }
        .login-form { 
            background: white; 
            padding: 40px; 
            border-radius: 10px; 
            box-shadow: 0 0 10px rgba(0,0,0,0.1); 
            width: 300px; 
        }
        .form-group { 
            margin-bottom: 20px; 
        }
        input { 
            width: 100%; 
            padding: 10px; 
            border: 1px solid #ddd; 
            border-radius: 5px; 
            box-sizing: border-box; 
        }
        button { 
            width: 100%; 
            padding: 10px; 
            background: #521D04; 
            color: white; 
            border: none; 
            border-radius: 5px; 
            cursor: pointer; 
        }
        .error { 
            color: red; 
            text-align: center; 
            background: #ffeaa7;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        .test-data {
            text-align: center; 
            margin-top: 15px; 
            color: #666; 
            font-size: 12px;
            background: #f9f9f9;
            padding: 10px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="login-form">
        <h2 style="text-align: center;">Вход в админку</h2>
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <input type="text" name="username" placeholder="Логин" required value="admin">
            </div>
            <div class="form-group">
                <input type="password" name="password" placeholder="Пароль" required value="admin123">
            </div>
            <button type="submit">Войти</button>
        </form>
        <div class="test-data">
            <strong>Тестовые данные:</strong><br>
            Логин: admin<br>
            Пароль: admin123
        </div>
    </div>
</body>
</html>
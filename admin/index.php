<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$database = new Database();
$db = $database->getConnection();

$new_orders = 0;
$cooperation_requests = 0;
$total_orders = 0;

if ($db) {
    try {
        $new_orders = $db->query("SELECT COUNT(*) FROM orders WHERE status = 'new'")->fetchColumn();
        $cooperation_requests = $db->query("SELECT COUNT(*) FROM cooperation_requests WHERE status = 'new'")->fetchColumn();
        $total_orders = $db->query("SELECT COUNT(*) FROM orders")->fetchColumn();
    } catch (Exception $e) {
    }
}
?>













<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Админ-панель</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; }
        .sidebar { 
            width: 250px; 
            background: #521D04; 
            color: white; 
            height: 100vh; 
            position: fixed; 
            padding: 20px; 
        }
        .main-content { 
            margin-left: 250px; 
            padding: 20px; 
        }
        .nav-item { 
            padding: 15px 0; 
            border-bottom: 1px solid #6b2d0a; 
        }
        .nav-item a { 
            color: white; 
            text-decoration: none; 
            display: block; 
        }
        .nav-item a:hover { 
            color: #F8EB9A; 
        }
        .stats { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); 
            gap: 20px; 
            margin-bottom: 30px; 
        }
        .stat-card { 
            background: white; 
            padding: 20px; 
            border-radius: 10px; 
            box-shadow: 0 2px 5px rgba(0,0,0,0.1); 
            text-align: center;
        }
        .stat-number { 
            font-size: 2em; 
            font-weight: bold; 
            color: #521D04; 
        }
        .header { 
            background: white; 
            padding: 20px; 
            margin-bottom: 20px; 
            border-radius: 10px; 
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Админ-панель</h2>
        <div style="padding: 10px 0; color: #F8EB9A;">Привет, <?php echo $_SESSION['username']; ?>!</div>
        <nav>
            <div class="nav-item"><a href="index.php">📊 Главная</a></div>
            <div class="nav-item"><a href="orders.php">📦 Заказы</a></div>
            <div class="nav-item"><a href="cooperation.php">🤝 Заявки</a></div>
            <div class="nav-item"><a href="logout.php">🚪 Выйти</a></div>
        </nav>
    </div>
    
    <div class="main-content">
        <div class="header">
            <h1>Панель управления</h1>
        </div>
        
        <div class="stats">
            <div class="stat-card">
                <div class="stat-number"><?php echo $new_orders; ?></div>
                <div>Новые заказы</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $cooperation_requests; ?></div>
                <div>Новые заявки</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_orders; ?></div>
                <div>Всего заказов</div>
            </div>
        </div>
        
        <div style="background: white; padding: 20px; border-radius: 10px;">
            <h2>Быстрые ссылки</h2>
            <div style="margin-top: 15px;">
                <a href="orders.php" style="display: block; padding: 10px; background: #f9f9f9; margin: 5px 0; border-radius: 5px; text-decoration: none; color: #521D04;">
                    📦 Управление заказами
                </a>
                <a href="cooperation.php" style="display: block; padding: 10px; background: #f9f9f9; margin: 5px 0; border-radius: 5px; text-decoration: none; color: #521D04;">
                    🤝 Заявки на сотрудничество
                </a>
            </div>
        </div>
    </div>
</body>
</html>

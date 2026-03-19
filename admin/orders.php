<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$database = new Database();
$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $query = "UPDATE orders SET status = :status WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':status', $_POST['status']);
    $stmt->bindParam(':id', $_POST['order_id']);
    $stmt->execute();
    header("Location: orders.php?updated=1");
    exit;
}


if (isset($_GET['export']) && $_GET['export'] == 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=orders_' . date('Y-m-d') . '.csv');
    
    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID', 'Имя', 'Телефон', 'Email', 'Сумма', 'Статус', 'Дата', 'Товары', 'Параметры'], ';');
    
    $query = "SELECT * FROM orders ORDER BY created_at DESC";
    $stmt = $db->query($query);
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $order_data = json_decode($row['order_data'], true);
        $items_text = '';
        $params_text = '';
        
        if (isset($order_data['items']) && !empty($order_data['items'])) {
            $items = [];
            foreach ($order_data['items'] as $item) {
                $items[] = ($item['quantity'] ?? 1) . '× ' . ($item['title'] ?? '');
                
                if (isset($item['parameters']) && !empty($item['parameters'])) {
                    foreach ($item['parameters'] as $param => $value) {
                        $params_text .= $param . ': ' . $value . '; ';
                    }
                }
            }
            $items_text = implode(', ', $items);
        }
        
        fputcsv($output, [
            $row['id'],
            $row['customer_name'],
            $row['phone'],
            $row['email'],
            $row['total_price'] . ' руб.',
            $row['status'],
            date('d.m.Y H:i', strtotime($row['created_at'])),
            $items_text,
            $params_text
        ], ';');
    }
    fclose($output);
    exit;
}

$query = "SELECT * FROM orders WHERE 1=1";
$params = [];

if (!empty($_GET['search'])) {
    $query .= " AND (customer_name LIKE :search OR phone LIKE :search OR email LIKE :search)";
    $params[':search'] = '%' . $_GET['search'] . '%';
}

if (!empty($_GET['status'])) {
    $query .= " AND status = :status";
    $params[':status'] = $_GET['status'];
}

if (!empty($_GET['date_from'])) {
    $query .= " AND DATE(created_at) >= :date_from";
    $params[':date_from'] = $_GET['date_from'];
}

if (!empty($_GET['date_to'])) {
    $query .= " AND DATE(created_at) <= :date_to";
    $params[':date_to'] = $_GET['date_to'];
}

$query .= " ORDER BY created_at DESC";

$stmt = $db->prepare($query);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

$status_stats = [
    'new' => 0,
    'confirmed' => 0,
    'in_progress' => 0,
    'completed' => 0,
    'cancelled' => 0
];

foreach ($orders as $order) {
    $status_stats[$order['status']]++;
}
?>









<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Управление заказами</title>
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
        .header { 
            background: white; 
            padding: 20px; 
            margin-bottom: 20px; 
            border-radius: 10px; 
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        .stat-item {
            background: white;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .stat-number {
            font-size: 1.5em;
            font-weight: bold;
            color: #521D04;
        }
        .table-container {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        th {
            background: #f9f9f9;
            font-weight: bold;
            color: #521D04;
        }
        tr:hover {
            background: #f9f9f9;
        }
        .status-new { background: #ffeaa7; color: #d63031; padding: 5px 10px; border-radius: 15px; font-size: 12px; }
        .status-confirmed { background: #74b9ff; color: white; padding: 5px 10px; border-radius: 15px; font-size: 12px; }
        .status-in_progress { background: #fdcb6e; color: white; padding: 5px 10px; border-radius: 15px; font-size: 12px; }
        .status-completed { background: #00b894; color: white; padding: 5px 10px; border-radius: 15px; font-size: 12px; }
        .status-cancelled { background: #dfe6e9; color: #636e72; padding: 5px 10px; border-radius: 15px; font-size: 12px; }
        select {
            padding: 5px;
            border-radius: 5px;
            border: 1px solid #ddd;
            background: white;
        }
        .btn {
            background: #521D04;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
        }
        .btn:hover {
            background: #3a1503;
        }
        .btn-export {
            background: #00b894;
        }
        .btn-export:hover {
            background: #00a085;
        }
        .alert {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Админ-панель</h2>
        <div style="padding: 10px 0; color: #F8EB9A;"><?php echo $_SESSION['username']; ?></div>
        <nav>
            <div class="nav-item"><a href="index.php">📊 Главная</a></div>
            <div class="nav-item"><a href="orders.php">📦 Заказы</a></div>
            <div class="nav-item"><a href="cooperation.php">🤝 Заявки</a></div>
            <div class="nav-item"><a href="logout.php">🚪 Выйти</a></div>
        </nav>
    </div>
    
    <div class="main-content">
        <div class="header">
            <h1>Управление заказами</h1>
            <div>
                <a href="?export=csv" class="btn btn-export">📥 Экспорт в CSV</a>
            </div>
        </div>
        
        <?php if (isset($_GET['updated'])): ?>
            <div class="alert">Статус заказа успешно обновлен!</div>
        <?php endif; ?>
        
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-number"><?php echo $status_stats['new']; ?></div>
                <div>Новые</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?php echo $status_stats['confirmed']; ?></div>
                <div>Подтверждены</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?php echo $status_stats['in_progress']; ?></div>
                <div>В работе</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?php echo $status_stats['completed']; ?></div>
                <div>Завершены</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?php echo $status_stats['cancelled']; ?></div>
                <div>Отменены</div>
            </div>
        </div>

<div style="background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px;">
    <form method="GET" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
        <input type="text" name="search" placeholder="Поиск по имени, телефону, email..." 
               value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>"
               style="flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
        
        <select name="status" style="padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
            <option value="">Все статусы</option>
            <option value="new" <?php echo ($_GET['status'] ?? '') == 'new' ? 'selected' : ''; ?>>Новые</option>
            <option value="confirmed" <?php echo ($_GET['status'] ?? '') == 'confirmed' ? 'selected' : ''; ?>>Подтвержденные</option>
            <option value="in_progress" <?php echo ($_GET['status'] ?? '') == 'in_progress' ? 'selected' : ''; ?>>В работе</option>
            <option value="completed" <?php echo ($_GET['status'] ?? '') == 'completed' ? 'selected' : ''; ?>>Завершенные</option>
            <option value="cancelled" <?php echo ($_GET['status'] ?? '') == 'cancelled' ? 'selected' : ''; ?>>Отмененные</option>
        </select>
        
        <input type="date" name="date_from" value="<?php echo htmlspecialchars($_GET['date_from'] ?? ''); ?>"
               style="padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
        <input type="date" name="date_to" value="<?php echo htmlspecialchars($_GET['date_to'] ?? ''); ?>"
               style="padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
        
        <button type="submit" class="btn">🔍 Найти</button>
        <a href="orders.php" class="btn btn-secondary">Сбросить</a>
    </form>
</div>
        <div class="table-container">
            <?php if (empty($orders)): ?>
                <p style="text-align: center; color: #666;">Заказов пока нет</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Клиент</th>
                            <th>Телефон</th>
                            <th>Email</th>
                            <th>Сумма</th>
                            <th>Статус</th>
                            <th>Дата</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                        <tr>
                            <td>#<?php echo $order['id']; ?></td>
                            <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                            <td><?php echo htmlspecialchars($order['phone']); ?></td>
                            <td><?php echo htmlspecialchars($order['email']); ?></td>
                            <td><?php echo number_format($order['total_price'], 2); ?> руб.</td>
                            <td>
                                <span class="status-<?php echo $order['status']; ?>">
                                    <?php 
                                    $status_labels = [
                                        'new' => 'Новый',
                                        'confirmed' => 'Подтвержден',
                                        'in_progress' => 'В работе',
                                        'completed' => 'Завершен',
                                        'cancelled' => 'Отменен'
                                    ];
                                    echo $status_labels[$order['status']];
                                    ?>
                                </span>
                            </td>
                            <td><?php echo date('d.m.Y H:i', strtotime($order['created_at'])); ?></td>
                            <td>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                    <select name="status" onchange="this.form.submit()">
                                        <option value="new" <?php echo $order['status'] == 'new' ? 'selected' : ''; ?>>Новый</option>
                                        <option value="confirmed" <?php echo $order['status'] == 'confirmed' ? 'selected' : ''; ?>>Подтвержден</option>
                                        <option value="in_progress" <?php echo $order['status'] == 'in_progress' ? 'selected' : ''; ?>>В работе</option>
                                        <option value="completed" <?php echo $order['status'] == 'completed' ? 'selected' : ''; ?>>Завершен</option>
                                        <option value="cancelled" <?php echo $order['status'] == 'cancelled' ? 'selected' : ''; ?>>Отменен</option>
                                    </select>
                                    <input type="hidden" name="update_status" value="1">
                                </form>
                                <a href="order_detail.php?id=<?php echo $order['id']; ?>" class="btn" style="margin-left: 10px; padding: 5px 10px; font-size: 12px; display: inline-block;">Подробнее</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

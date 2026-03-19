<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: orders.php");
    exit;
}

$database = new Database();
$db = $database->getConnection();

$query = "SELECT * FROM orders WHERE id = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $_GET['id']);
$stmt->execute();
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    header("Location: orders.php");
    exit;
}

$order_data = json_decode($order['order_data'], true);
?>










<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Детали заказа #<?php echo $order['id']; ?></title>
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
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .header {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .back-btn {
            background: #521D04;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
        }
        .back-btn:hover {
            background: #3a1503;
        }
        .status-badge {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
        }
        .status-new { background: #ffeaa7; color: #d63031; }
        .status-confirmed { background: #74b9ff; color: white; }
        .status-in_progress { background: #fdcb6e; color: white; }
        .status-completed { background: #00b894; color: white; }
        .status-cancelled { background: #dfe6e9; color: #636e72; }
        .order-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .info-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .info-card h3 {
            color: #521D04;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f5e27a;
        }
        .info-row {
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        .info-label {
            font-weight: bold;
            color: #666;
            display: inline-block;
            width: 120px;
        }
        .items-container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .item-card {
            border: 1px solid #eee;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            background: #f9f9f9;
        }
        .item-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        .item-title {
            font-size: 18px;
            font-weight: bold;
            color: #521D04;
        }
        .item-price {
            font-weight: bold;
            color: #00b894;
        }
        .item-image {
            max-width: 150px;
            max-height: 150px;
            border-radius: 8px;
            margin: 10px 0;
        }
        .parameters {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px dashed #ddd;
        }
        .param-item {
            margin-bottom: 5px;
            padding-left: 15px;
        }
        .param-name {
            font-weight: bold;
            color: #666;
        }
        .json-viewer {
            background: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            margin-top: 20px;
            font-family: monospace;
            font-size: 12px;
            white-space: pre-wrap;
            max-height: 300px;
            overflow-y: auto;
        }
        .action-buttons {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        .btn {
            padding: 10px 20px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        .btn-primary {
            background: #521D04;
            color: white;
        }
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        .btn-danger {
            background: #dc3545;
            color: white;
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
            <div>
                <h1>Заказ #<?php echo $order['id']; ?></h1>
                <p style="color: #666; margin-top: 5px;">
                    <?php echo date('d.m.Y H:i', strtotime($order['created_at'])); ?>
                    | 
                    <span class="status-badge status-<?php echo $order['status']; ?>">
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
                </p>
            </div>
            <a href="orders.php" class="back-btn">← Назад к списку</a>
        </div>
        
        <div class="container">
            <!-- Информация о клиенте и доставке -->
            <div class="order-info-grid">
                <div class="info-card">
                    <h3>Информация о клиенте</h3>
                    <div class="info-row">
                        <span class="info-label">Имя:</span>
                        <?php echo htmlspecialchars($order['customer_name']); ?>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Телефон:</span>
                        <a href="tel:<?php echo htmlspecialchars($order['phone']); ?>">
                            <?php echo htmlspecialchars($order['phone']); ?>
                        </a>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Email:</span>
                        <a href="mailto:<?php echo htmlspecialchars($order['email']); ?>">
                            <?php echo htmlspecialchars($order['email']); ?>
                        </a>
                    </div>
                    <?php if (!empty($order['wishes'])): ?>
                    <div class="info-row">
                        <span class="info-label">Пожелания:</span>
                        <?php echo htmlspecialchars($order['wishes']); ?>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="info-card">
                    <h3>Доставка и оплата</h3>
                    <div class="info-row">
                        <span class="info-label">Способ:</span>
                        <?php echo $order['delivery_method'] == 'delivery' ? 'Доставка' : 'Самовывоз'; ?>
                    </div>
                    <?php if ($order['delivery_method'] == 'delivery' && !empty($order['address'])): ?>
                    <div class="info-row">
                        <span class="info-label">Адрес:</span>
                        <?php echo htmlspecialchars($order['address']); ?>
                    </div>
                    <?php endif; ?>
                    <div class="info-row">
                        <span class="info-label">Сумма:</span>
                        <strong style="color: #521D04; font-size: 18px;">
                            <?php echo number_format($order['total_price'], 2); ?> руб.
                        </strong>
                    </div>
                </div>
            </div>
            
            <!-- Состав заказа -->
            <div class="items-container">
                <h2 style="color: #521D04; margin-bottom: 20px;">Состав заказа</h2>
                
                <?php if (isset($order_data['items']) && !empty($order_data['items'])): ?>
                    <?php foreach ($order_data['items'] as $index => $item): ?>
                        <div class="item-card">
                            <div class="item-header">
                                <div class="item-title">
                                    <?php echo ($index + 1) . '. ' . htmlspecialchars($item['title'] ?? 'Неизвестный товар'); ?>
                                </div>
                                <div class="item-price">
                                    <?php echo ($item['unit_price'] ?? 0) * ($item['quantity'] ?? 1); ?> руб.
                                    <span style="color: #666; font-size: 14px;">
                                        (<?php echo $item['unit_price'] ?? 0; ?> руб. × <?php echo $item['quantity'] ?? 1; ?> шт.)
                                    </span>
                                </div>
                            </div>
                            
                            <div>
                                <strong>Тип:</strong> <?php echo htmlspecialchars($item['type'] ?? 'не указан'); ?>
                                <br>
                                <strong>Количество:</strong> <?php echo $item['quantity'] ?? 1; ?> шт.
                            </div>
                            
                            <?php if (isset($item['parameters']) && !empty($item['parameters'])): ?>
                                <div class="parameters">
                                    <strong>Параметры:</strong>
                                    <?php foreach ($item['parameters'] as $paramName => $paramValue): ?>
                                        <div class="param-item">
                                            <span class="param-name"><?php echo htmlspecialchars($paramName); ?>:</span>
                                            <?php echo htmlspecialchars($paramValue); ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (isset($item['image']) && !empty($item['image'])): ?>
                                <div style="margin-top: 10px;">
                                    <strong>Изображение:</strong><br>
                                    <img src="<?php echo htmlspecialchars($item['image']); ?>" 
                                         alt="<?php echo htmlspecialchars($item['title'] ?? ''); ?>" 
                                         class="item-image"
                                         onclick="window.open('<?php echo htmlspecialchars($item['image']); ?>', '_blank')"
                                         style="cursor: pointer;">
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="text-align: center; color: #666; padding: 20px;">
                        Информация о товарах недоступна
                    </p>
                <?php endif; ?>
                
                <div style="text-align: right; margin-top: 20px; padding-top: 20px; border-top: 2px solid #521D04;">
                    <h3 style="color: #521D04;">
                        Итого: <?php echo number_format($order['total_price'], 2); ?> руб.
                    </h3>
                </div>
            </div>
            
            <!-- Полные данные JSON (для отладки) -->
            <details style="margin-bottom: 20px;">
                <summary style="padding: 10px; background: #f9f9f9; border-radius: 5px; cursor: pointer;">
                    📄 Показать полные данные заказа (JSON)
                </summary>
                <div class="json-viewer">
<?php 
echo htmlspecialchars(json_encode($order_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
?>
                </div>
            </details>
            
            <!-- Кнопки действий -->
            <div class="action-buttons">
                <form method="POST" action="orders.php" style="display: inline;">
                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                    <select name="status" onchange="this.form.submit()" class="btn" style="background: #521D04; color: white; padding: 10px 15px;">
                        <option value="">Изменить статус...</option>
                        <option value="new" <?php echo $order['status'] == 'new' ? 'selected' : ''; ?>>Новый</option>
                        <option value="confirmed" <?php echo $order['status'] == 'confirmed' ? 'selected' : ''; ?>>Подтвержден</option>
                        <option value="in_progress" <?php echo $order['status'] == 'in_progress' ? 'selected' : ''; ?>>В работе</option>
                        <option value="completed" <?php echo $order['status'] == 'completed' ? 'selected' : ''; ?>>Завершен</option>
                        <option value="cancelled" <?php echo $order['status'] == 'cancelled' ? 'selected' : ''; ?>>Отменен</option>
                    </select>
                    <input type="hidden" name="update_status" value="1">
                </form>
                
                <a href="orders.php" class="btn btn-secondary">Назад к списку</a>
                
                <?php if ($order['status'] != 'cancelled'): ?>
                <form method="POST" action="orders.php" style="display: inline;" onsubmit="return confirm('Вы уверены, что хотите отменить этот заказ?');">
                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                    <input type="hidden" name="status" value="cancelled">
                    <input type="hidden" name="update_status" value="1">
                    <button type="submit" class="btn btn-danger">Отменить заказ</button>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script>
    // Функция для печати заказа
    function printOrder() {
        window.print();
    }
    
    // Функция для копирования данных заказа
    function copyOrderData() {
        const orderData = <?php echo json_encode($order_data); ?>;
        const text = `Заказ #${<?php echo $order['id']; ?>}\n` +
                     `Клиент: ${<?php echo json_encode($order['customer_name']); ?>}\n` +
                     `Телефон: ${<?php echo json_encode($order['phone']); ?>}\n` +
                     `Сумма: ${<?php echo $order['total_price']; ?>} руб.\n` +
                     `Статус: ${<?php echo json_encode($status_labels[$order['status']]); ?>}`;
        
        navigator.clipboard.writeText(text)
            .then(() => alert('Данные скопированы в буфер обмена'))
            .catch(err => console.error('Ошибка копирования:', err));
    }
    </script>
</body>
</html>

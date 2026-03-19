<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json; charset=utf-8');

ob_start();

$log_file = dirname(__DIR__) . '/order_debug.log';
$log = "[" . date('Y-m-d H:i:s') . "] ======== НОВЫЙ ЗАКАЗ ========\n";

try {
    $log .= "REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD'] . "\n";
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Только POST запросы');
    }
    if (empty($_POST)) {
        $raw_input = file_get_contents('php://input');
        
        if (!empty($raw_input)) {
            $_POST = json_decode($raw_input, true);
        }
        
        if (empty($_POST)) {
            throw new Exception('Нет данных в запросе');
        }
    }

    $customer_name = trim($_POST['full_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $delivery_method = trim($_POST['delivery_type'] ?? 'pickup');
    $address = trim($_POST['address'] ?? '');
    $wishes = trim($_POST['wishes'] ?? '');
    $order_items_json = $_POST['order_items'] ?? '[]';
    $total_price = floatval($_POST['total_price'] ?? 0);

    $log .= "Данные клиента:\n";
    $log .= "- Имя: '$customer_name'\n";
    $log .= "- Телефон: '$phone'\n";
    $log .= "- Email: '$email'\n";
    $log .= "- Доставка: '$delivery_method'\n";
    $log .= "- Адрес: '$address'\n";
    $log .= "- Пожелания: '$wishes'\n";
    $log .= "- Сумма: '$total_price'\n";
    $log .= "- JSON длина: " . strlen($order_items_json) . "\n";

    $errors = [];
    
    if (empty($customer_name)) {
        $errors[] = 'Имя обязательно для заполнения';
    }
    
    if (empty($phone)) {
        $errors[] = 'Телефон обязателен для заполнения';
    }
    
    if (empty($email)) {
        $errors[] = 'Email обязателен для заполнения';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Неверный формат email';
    }
    
    if (!in_array($delivery_method, ['pickup', 'delivery'])) {
        $errors[] = 'Неверный метод доставки';
    }
    
    if ($delivery_method === 'delivery' && empty($address)) {
        $errors[] = 'Адрес доставки обязателен при выборе доставки';
    }

    if ($total_price <= 0) {
        $errors[] = 'Сумма заказа должна быть больше 0';
    }
    
    $order_items = json_decode($order_items_json, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        $errors[] = 'Ошибка в данных заказа: ' . json_last_error_msg();
        $log .= "JSON decode error: " . json_last_error_msg() . "\n";
    } elseif (empty($order_items) || !is_array($order_items)) {
        $errors[] = 'Корзина пуста. Добавьте товары в заказ.';
    }
    
    $log .= "Ошибки валидации: " . count($errors) . "\n";
    if (!empty($errors)) {
        $log .= "Детали ошибок: " . implode(', ', $errors) . "\n";
        throw new Exception(implode('. ', $errors));
    }
    
    $log .= "Валидация пройдена успешно\n";

    $log .= "Детали позиций заказа:\n";
    foreach ($order_items as $index => $item) {
        $log .= "Позиция #" . ($index + 1) . ":\n";
        $log .= "- Тип: " . ($item['type'] ?? 'не указан') . "\n";
        $log .= "- Название: " . ($item['title'] ?? 'не указано') . "\n";
        $log .= "- Количество: " . ($item['quantity'] ?? 1) . "\n";
        $log .= "- Цена за шт: " . ($item['basePrice'] ?? 0) . "\n";
        $log .= "- Сумма: " . (($item['basePrice'] ?? 0) * ($item['quantity'] ?? 1)) . "\n";
        
        if (isset($item['selectedParams']) && !empty($item['selectedParams'])) {
            $log .= "- Параметры:\n";
            foreach ($item['selectedParams'] as $paramName => $paramValue) {
                $log .= "  * $paramName: $paramValue\n";
            }
        }
        $log .= "\n";
    }

    require_once dirname(__DIR__) . '/config/database.php';
    $database = new Database();
    $db = $database->getConnection();
    
    if (!$db) {
        throw new Exception('Не удалось подключиться к базе данных');
    }

    $order_data = [
        'customer' => [
            'name' => $customer_name,
            'phone' => $phone,
            'email' => $email,
            'wishes' => $wishes
        ],
        'delivery' => [
            'method' => $delivery_method,
            'address' => $delivery_method === 'delivery' ? $address : null
        ],
        'items' => [],
        'summary' => [
            'total_price' => $total_price,
            'total_items' => count($order_items),
            'created_at' => date('Y-m-d H:i:s')
        ]
    ];

    foreach ($order_items as $item) {
        $item_details = [
            'type' => $item['type'] ?? 'unknown',
            'title' => $item['title'] ?? 'Неизвестный десерт',
            'quantity' => $item['quantity'] ?? 1,
            'unit_price' => $item['basePrice'] ?? 0,
            'total_price' => ($item['basePrice'] ?? 0) * ($item['quantity'] ?? 1),
            'image' => $item['image'] ?? null
        ];

        if (isset($item['selectedParams']) && !empty($item['selectedParams'])) {
            $item_details['parameters'] = $item['selectedParams'];
        }

        if (isset($item['id'])) $item_details['cart_id'] = $item['id'];
        if (isset($item['addedAt'])) $item_details['added_at'] = $item['addedAt'];

        $order_data['items'][] = $item_details;
    }

    $order_json = json_encode($order_data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Ошибка создания JSON: ' . json_last_error_msg());
    }

    $sql = "INSERT INTO orders (
                customer_name, 
                phone, 
                email, 
                delivery_method, 
                address, 
                wishes,
                order_data, 
                total_price, 
                status,
                created_at
            ) VALUES (
                :customer_name,
                :phone,
                :email,
                :delivery_method,
                :address,
                :wishes,
                :order_data,
                :total_price,
                'new',
                NOW()
            )";

    $stmt = $db->prepare($sql);
    
    $stmt->bindParam(':customer_name', $customer_name);
    $stmt->bindParam(':phone', $phone);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':delivery_method', $delivery_method);
    $stmt->bindParam(':address', $address);
    $stmt->bindParam(':wishes', $wishes);
    $stmt->bindParam(':order_data', $order_json);
    $stmt->bindParam(':total_price', $total_price);

    if ($stmt->execute()) {
        $order_id = $db->lastInsertId();
        
        $log .= " Заказ #$order_id успешно сохранен\n";
        $log .= "=================================\n\n";
        
        echo json_encode([
            'success' => true,
            'message' => 'Заказ успешно оформлен! Наш администратор свяжется с вами в ближайшее время.',
            'order_id' => $order_id
        ], JSON_UNESCAPED_UNICODE);

    } else {
        $errorInfo = $stmt->errorInfo();
        throw new Exception('Ошибка базы данных: ' . ($errorInfo[2] ?? 'неизвестная ошибка'));
    }

} catch (Exception $e) {
    $error_log = " ОШИБКА: " . $e->getMessage() . "\n";
    $error_log .= "=================================\n\n";
    
    file_put_contents($log_file, $error_log, FILE_APPEND);

    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

file_put_contents($log_file, $log, FILE_APPEND);
ob_end_flush();
?>
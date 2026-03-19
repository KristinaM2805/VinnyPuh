<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json; charset=utf-8');

$log_file = dirname(__DIR__) . '/cooperation_debug.log';
$log = "[" . date('Y-m-d H:i:s') . "] ======== НОВАЯ ЗАЯВКА ========\n";

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Только POST запросы');
    }

    $full_name = trim($_POST['full_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $company = trim($_POST['company'] ?? '');
    $message = trim($_POST['message'] ?? '');

    $log .= "Данные:\n";
    $log .= "- Имя: '$full_name'\n";
    $log .= "- Телефон: '$phone'\n";
    $log .= "- Компания: '$company'\n";
    $log .= "- Сообщение: '$message'\n";

    $errors = [];
    
    if (empty($full_name)) {
        $errors['full_name'] = 'Введите ваше имя';
    } elseif (strlen($full_name) < 2) {
        $errors['full_name'] = 'Имя слишком короткое (минимум 2 символа)';
    }
    
    if (empty($phone)) {
        $errors['phone'] = 'Введите телефон';
    } else {
        $clean_phone = preg_replace('/[^0-9+]/', '', $phone);
        if (strlen($clean_phone) < 7) {
            $errors['phone'] = 'Телефон слишком короткий (минимум 7 цифр)';
        }
    }
    
    if (empty($message)) {
        $errors['message'] = 'Введите сообщение';
    } elseif (strlen($message) < 10) {
        $errors['message'] = 'Сообщение слишком короткое (минимум 10 символов)';
    }
    
    if (!empty($errors)) {
        throw new Exception('Ошибки валидации');
    }

    require_once dirname(__DIR__) . '/config/database.php';
    $database = new Database();
    $db = $database->getConnection();
    
    if (!$db) {
        throw new Exception('Не удалось подключиться к базе данных');
    }
    $sql = "INSERT INTO cooperation_requests 
            (full_name, phone, company, message, status, created_at) 
            VALUES 
            (:full_name, :phone, :company, :message, 'new', NOW())";

    $stmt = $db->prepare($sql);
    
    $stmt->bindParam(':full_name', $full_name);
    $stmt->bindParam(':phone', $phone);
    $stmt->bindParam(':company', $company);
    $stmt->bindParam(':message', $message);

    if ($stmt->execute()) {
        $request_id = $db->lastInsertId();
        
        $log .= "✅ Заявка #$request_id сохранена\n";
    
        sendEmailNotification($full_name, $phone, $company, $message);
        
        echo json_encode([
            'success' => true,
            'message' => 'Заявка отправлена успешно! Мы свяжемся с вами в ближайшее время.',
            'request_id' => $request_id
        ], JSON_UNESCAPED_UNICODE);

    } else {
        $errorInfo = $stmt->errorInfo();
        throw new Exception('Ошибка базы данных: ' . ($errorInfo[2] ?? 'неизвестная ошибка'));
    }

} catch (Exception $e) {
    $error_log = "❌ ОШИБКА: " . $e->getMessage() . "\n";
    
    http_response_code(400);
    
    if ($e->getMessage() === 'Ошибки валидации') {
        echo json_encode([
            'success' => false,
            'message' => 'Пожалуйста, исправьте ошибки в форме',
            'errors' => $errors ?? []
        ], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
    }
}

file_put_contents($log_file, $log . "=================================\n\n", FILE_APPEND);

function sendEmailNotification($name, $phone, $company, $message) {
    $to = "kristinamiakushina2805@gmail.com";
    $subject = "Новая заявка на сотрудничество от " . $name;
    
    $body = "Поступила новая заявка на сотрудничество:\n\n";
    $body .= "Имя: " . $name . "\n";
    $body .= "Телефон: " . $phone . "\n";
    $body .= "Компания: " . ($company ?: 'не указана') . "\n";
    $body .= "Сообщение:\n" . $message . "\n\n";
    $body .= "Дата: " . date('d.m.Y H:i:s') . "\n";
    
    $headers = "From: no-reply@vinnipux.by\r\n";
    $headers .= "Content-Type: text/plain; charset=utf-8\r\n";
    
    @mail($to, $subject, $body, $headers);
}
?>
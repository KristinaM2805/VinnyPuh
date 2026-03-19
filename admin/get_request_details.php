<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    die('Ошибка авторизации');
}

if (!isset($_GET['id'])) {
    die('ID не указан');
}

$database = new Database();
$db = $database->getConnection();

$query = "SELECT * FROM cooperation_requests WHERE id = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $_GET['id']);
$stmt->execute();
$request = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$request) {
    die('Заявка не найдена');
}

$status_labels = [
    'new' => 'Новая',
    'processed' => 'В работе',
    'completed' => 'Завершена'
];
?>





<div style="padding: 20px;">
    <h2>Детали заявки #<?php echo $request['id']; ?></h2>
    
    <div style="margin-top: 20px;">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="padding: 10px; border-bottom: 1px solid #eee; font-weight: bold; width: 150px;">Имя:</td>
                <td style="padding: 10px; border-bottom: 1px solid #eee;"><?php echo htmlspecialchars($request['full_name']); ?></td>
            </tr>
            <tr>
                <td style="padding: 10px; border-bottom: 1px solid #eee; font-weight: bold;">Телефон:</td>
                <td style="padding: 10px; border-bottom: 1px solid #eee;"><?php echo htmlspecialchars($request['phone']); ?></td>
            </tr>
            <tr>
                <td style="padding: 10px; border-bottom: 1px solid #eee; font-weight: bold;">Компания:</td>
                <td style="padding: 10px; border-bottom: 1px solid #eee;"><?php echo htmlspecialchars($request['company']); ?></td>
            </tr>
            <tr>
                <td style="padding: 10px; border-bottom: 1px solid #eee; font-weight: bold;">Статус:</td>
                <td style="padding: 10px; border-bottom: 1px solid #eee;">
                    <span style="background: #ffeaa7; padding: 5px 10px; border-radius: 15px;">
                        <?php echo $status_labels[$request['status']]; ?>
                    </span>
                </td>
            </tr>
            <tr>
                <td style="padding: 10px; border-bottom: 1px solid #eee; font-weight: bold;">Дата:</td>
                <td style="padding: 10px; border-bottom: 1px solid #eee;"><?php echo date('d.m.Y H:i', strtotime($request['created_at'])); ?></td>
            </tr>
            <tr>
                <td style="padding: 10px; font-weight: bold; vertical-align: top;">Сообщение:</td>
                <td style="padding: 10px;">
                    <div style="background: #f9f9f9; padding: 15px; border-radius: 5px; white-space: pre-wrap;">
                        <?php echo nl2br(htmlspecialchars($request['message'])); ?>
                    </div>
                </td>
            </tr>
        </table>
    </div>
    
    <div style="margin-top: 20px; text-align: right;">
        <button onclick="closeModal()" style="padding: 10px 20px; background: #521D04; color: white; border: none; border-radius: 5px; cursor: pointer;">
            Закрыть
        </button>
    </div>
</div>

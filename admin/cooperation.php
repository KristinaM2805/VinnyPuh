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
    $query = "UPDATE cooperation_requests SET status = :status WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':status', $_POST['status']);
    $stmt->bindParam(':id', $_POST['request_id']);
    $stmt->execute();
    header("Location: cooperation.php?updated=1");
    exit;
}

if (isset($_GET['delete'])) {
    $query = "DELETE FROM cooperation_requests WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $_GET['delete']);
    $stmt->execute();
    header("Location: cooperation.php?deleted=1");
    exit;
}

if (isset($_GET['export']) && $_GET['export'] == 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=cooperation_requests_' . date('Y-m-d') . '.csv');
    
    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID', 'Имя', 'Телефон', 'Компания', 'Сообщение', 'Статус', 'Дата'], ';');
    
    $query = "SELECT * FROM cooperation_requests ORDER BY created_at DESC";
    $stmt = $db->query($query);
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($output, [
            $row['id'],
            $row['full_name'],
            $row['phone'],
            $row['company'],
            substr($row['message'], 0, 100) . '...',
            $row['status'],
            date('d.m.Y H:i', strtotime($row['created_at']))
        ], ';');
    }
    fclose($output);
    exit;
}

$query = "SELECT * FROM cooperation_requests ORDER BY created_at DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
$status_stats = ['new' => 0, 'processed' => 0, 'completed' => 0];
foreach ($requests as $request) {
    $status_stats[$request['status']]++;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Заявки на сотрудничество</title>
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
        .status-processed { background: #74b9ff; color: white; padding: 5px 10px; border-radius: 15px; font-size: 12px; }
        .status-completed { background: #00b894; color: white; padding: 5px 10px; border-radius: 15px; font-size: 12px; }
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
        .btn-delete {
            background: #e17055;
        }
        .btn-delete:hover {
            background: #d63031;
        }
        .alert {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
        }
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 10px;
            max-width: 600px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
        }
        .close-modal {
            float: right;
            font-size: 24px;
            cursor: pointer;
            color: #666;
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
            <h1>Заявки на сотрудничество</h1>
            <div>
                <a href="?export=csv" class="btn btn-export">📥 Экспорт в CSV</a>
            </div>
        </div>
        
        <?php if (isset($_GET['updated'])): ?>
            <div class="alert">Статус заявки обновлен!</div>
        <?php endif; ?>
        
        <?php if (isset($_GET['deleted'])): ?>
            <div class="alert">Заявка удалена!</div>
        <?php endif; ?>
        
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-number"><?php echo $status_stats['new']; ?></div>
                <div>Новые</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?php echo $status_stats['processed']; ?></div>
                <div>В работе</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?php echo $status_stats['completed']; ?></div>
                <div>Завершены</div>
            </div>
        </div>
        
        <div class="table-container">
            <?php if (empty($requests)): ?>
                <p style="text-align: center; color: #666;">Заявок пока нет</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Имя</th>
                            <th>Телефон</th>
                            <th>Компания</th>
                            <th>Сообщение</th>
                            <th>Статус</th>
                            <th>Дата</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($requests as $request): ?>
                        <tr>
                            <td>#<?php echo $request['id']; ?></td>
                            <td><?php echo htmlspecialchars($request['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($request['phone']); ?></td>
                            <td><?php echo htmlspecialchars($request['company']); ?></td>
                            <td>
                                <?php 
                                $message = htmlspecialchars($request['message']);
                                echo strlen($message) > 50 ? substr($message, 0, 50) . '...' : $message;
                                ?>
                            </td>
                            <td>
                                <span class="status-<?php echo $request['status']; ?>">
                                    <?php 
                                    $status_labels = [
                                        'new' => 'Новая',
                                        'processed' => 'В работе',
                                        'completed' => 'Завершена'
                                    ];
                                    echo $status_labels[$request['status']];
                                    ?>
                                </span>
                            </td>
                            <td><?php echo date('d.m.Y H:i', strtotime($request['created_at'])); ?></td>
                            <td>
                                <form method="POST" style="display: inline-block;">
                                    <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                                    <select name="status" onchange="this.form.submit()">
                                        <option value="new" <?php echo $request['status'] == 'new' ? 'selected' : ''; ?>>Новая</option>
                                        <option value="processed" <?php echo $request['status'] == 'processed' ? 'selected' : ''; ?>>В работе</option>
                                        <option value="completed" <?php echo $request['status'] == 'completed' ? 'selected' : ''; ?>>Завершена</option>
                                    </select>
                                    <input type="hidden" name="update_status" value="1">
                                </form>
                                <button onclick="showRequestDetails(<?php echo $request['id']; ?>)" class="btn" style="margin-left: 10px; padding: 5px 10px; font-size: 12px;">
                                    Подробнее
                                </button>
                                <a href="?delete=<?php echo $request['id']; ?>" 
                                   class="btn btn-delete" 
                                   style="margin-left: 5px; padding: 5px 10px; font-size: 12px;"
                                   onclick="return confirm('Удалить эту заявку?')">
                                    Удалить
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
    <div id="requestModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal()">&times;</span>
            <div id="modalContent">
            </div>
        </div>
    </div>
    
    <script>
    function showRequestDetails(requestId) {

        fetch(`get_request_details.php?id=${requestId}`)
            .then(response => response.text())
            .then(data => {
                document.getElementById('modalContent').innerHTML = data;
                document.getElementById('requestModal').style.display = 'flex';
            });
    }
    
    function closeModal() {
        document.getElementById('requestModal').style.display = 'none';
    }
    window.onclick = function(event) {
        const modal = document.getElementById('requestModal');
        if (event.target === modal) {
            closeModal();
        }
    }
    </script>
</body>
</html>

<?php
class Database {
    private $host = "localhost";
    private $db_name = "confectionery_db";
    private $username = "root";
    private $password = "root";
    private $port = "8889";
    
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $dsn = "mysql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->db_name;
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->exec("set names utf8mb4");
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            if (isset($_GET['debug'])) {
                echo " Подключено к БД: " . $this->db_name . " на порту " . $this->port . "<br>";
            }
            
        } catch(PDOException $exception) {
            error_log("Connection error: " . $exception->getMessage());
            if (isset($_GET['debug'])) {
                echo "Ошибка подключения: " . $exception->getMessage() . "<br>";
            }
            return false;
        }
        return $this->conn;
    }

    public function testConnection() {
        $conn = $this->getConnection();
        if ($conn) {
            try {
                $result = $conn->query("SELECT DATABASE() as db")->fetch(PDO::FETCH_ASSOC);
                return "Подключено к БД: " . $result['db'];
            } catch (Exception $e) {
                return "Ошибка: " . $e->getMessage();
            }
        }
        return "Нет подключения";
    }
}
?>
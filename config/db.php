<?php
class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        try {
            $host = 'localhost';
            $dbname = 'productive_families_system';
            $username = 'root';
            $password = 'nahari';
            $charset = 'utf8mb4';
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
            $this->connection = new PDO($dsn, $username, $password, $options);
            
        } catch (PDOException $e) {
            error_log("Database connection error: " . $e->getMessage());
            die("عذراً، حدث خطأ في الاتصال بقاعدة البيانات");
        }
    }
    
    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new Database();
        }
        return self::$instance->connection;
    }
}
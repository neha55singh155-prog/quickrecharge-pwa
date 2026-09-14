<?php
/**
 * Database Helper
 * PDO connection singleton
 */

class Database {
    private static $instance = null;
    private $connection;
    
    private $host = 'localhost';
    private $dbname = 'recharge_app';
    private $username = 'root';
    private $password = '';
    
    private function __construct() {
        try {
            $this->connection = new PDO(
                "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4",
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            Response::error('Database connection failed', 500);
            exit;
        }
    }
    
    public static function getConnection() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance->connection;
    }
    
    /**
     * Execute a prepared statement
     */
    public static function query($sql, $params = []) {
        $db = self::getConnection();
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
    
    /**
     * Fetch single row
     */
    public static function fetchOne($sql, $params = []) {
        $stmt = self::query($sql, $params);
        return $stmt->fetch();
    }
    
    /**
     * Fetch all rows
     */
    public static function fetchAll($sql, $params = []) {
        $stmt = self::query($sql, $params);
        return $stmt->fetchAll();
    }
    
    /**
     * Insert and return last ID
     */
    public static function insert($sql, $params = []) {
        $stmt = self::query($sql, $params);
        return self::getConnection()->lastInsertId();
    }
    
    /**
     * Update affected rows
     */
    public static function update($sql, $params = []) {
        $stmt = self::query($sql, $params);
        return $stmt->rowCount();
    }
}

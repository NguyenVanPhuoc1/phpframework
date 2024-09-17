<?php
namespace app\core;
use \PDO;
use \PDOException;
class Database {
    private static $pdo;
    private static $dbConfig;

    public static function init() {
        self::$dbConfig = Registry::getIntance()->config['db'];
    }

    public static function connect() {
        if (!self::$pdo) {
            // Sử dụng self::$dbConfig để lấy cấu hình cơ sở dữ liệu
            self::init();
            $dbConfig = self::$dbConfig;
            // var_dump( $dbConfig);die();
            $dsn = "mysql:host={$dbConfig['DB_HOST']};dbname={$dbConfig['DB_NAME']};charset={$dbConfig['DB_CHARSET']}";
            try {
                self::$pdo = new PDO($dsn, $dbConfig['DB_USER'], $dbConfig['DB_PASSWORD']);
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Connection failed: " . $e->getMessage());
            }
        }
        return self::$pdo;
    }
}

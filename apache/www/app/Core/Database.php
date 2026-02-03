<?php
declare(strict_types=1);

namespace Unibostu\Core;
use PDO;

/**
 * Singleton PDO connection manager.
 */
class Database {
    private static ?PDO $pdo = null;

    private function __construct() {
        throw new \Exception('Not implemented');
    }

    /**
     * Returns the shared PDO connection, creating it if needed.
     *
     * @return PDO Shared connection instance.
     */
    public static function getConnection(): PDO {
        if (isset(self::$pdo)) {
            return self::$pdo;
        }
        $database = getenv("MYSQL_DATABASE");
        $username = getenv("MYSQL_USER");
        $password = trim(file_get_contents(getenv("MYSQL_PASSWORD_FILE")));
        $dsn = "mysql:host=db;dbname=" . $database . ";charset=utf8mb4";
        self::$pdo ??= new PDO($dsn, $username, $password);
        return self::$pdo; 
    }
}

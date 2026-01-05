<?php
declare(strict_types=1);

namespace Unibostu\Core;
use PDO;

class Database {
    private static ?PDO $pdo = null;

    private function __construct() {
        throw new \Exception('Not implemented');
    }

    public static function getConnection(): PDO {
        if (isset(self::$pdo)) {
            return self::$pdo;
        }
        $database = getenv("MYSQL_DATABASE");
        $username = getenv("MYSQL_USER");
        $password = trim(file_get_contents(getenv("MYSQL_PASSWORD_FILE")));
        $dsn = "mysql:host=db;dbname=" . $database;
        self::$pdo ??= new PDO($dsn, $username, $password);
        return self::$pdo; 
    }
}

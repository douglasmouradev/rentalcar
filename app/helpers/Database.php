<?php

declare(strict_types=1);

final class Database
{
    private static ?PDO $pdo = null;

    public static function pdo(): PDO
    {
        if (self::$pdo instanceof PDO) {
            return self::$pdo;
        }
        $c = require BASE_PATH . '/config/database.php';
        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            $c['host'],
            $c['port'],
            $c['database'],
            $c['charset']
        );
        $opts = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];
        $pdoClass = new ReflectionClass(PDO::class);
        if ($pdoClass->hasConstant('MYSQL_ATTR_CONNECT_TIMEOUT')) {
            $opts[PDO::MYSQL_ATTR_CONNECT_TIMEOUT] = 5;
        }
        self::$pdo = new PDO($dsn, $c['username'], $c['password'], $opts);
        return self::$pdo;
    }
}

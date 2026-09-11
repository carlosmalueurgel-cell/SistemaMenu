<?php

require_once __DIR__ . '/config.php';

class ConexionPDO
{
    private static ?PDO $cnn = null;

    public static function connect(): PDO
    {
        if (self::$cnn instanceof PDO) return self::$cnn;

        $dsn = 'mysql:host=' . HOST . ';port=' . PORT . ';dbname=' . DATABASE . ';charset=' . CHARSET;
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {
            self::$cnn = new PDO($dsn, USERNAME, PASSWORD, $options);
        } catch (PDOException $error) {
            throw new RuntimeException('No se pudo conectar a la base de datos', 0, $error);
        }
        return self::$cnn;
    }

    public static function query(string $sql, array $param = []): array
    {
        $stmt = self::connect()->prepare($sql);
        $stmt->execute($param);
        return $stmt->fetchAll();
    }

    public static function execute(string $sql, array $param = [], bool $retornarId = false)
    {
        $db = self::connect();
        $stmt = $db->prepare($sql);
        $stmt->execute($param);
        return $retornarId ? $db->lastInsertId() : $stmt->rowCount() > 0;
    }
}

<?php
/** Q5: Database class - PDO wrapped in a SINGLETON (one connection per request). */
final class Database
{
    private static ?Database $instance = null;
    private PDO $pdo;

    private function __construct()
    {
        $c   = config('db');
        $dsn = "mysql:host={$c['host']};dbname={$c['name']};charset={$c['charset']}";
        $this->pdo = new PDO($dsn, $c['user'], $c['pass'], [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    }
    private function __clone() {}
    public function __wakeup() { throw new Exception('Cannot unserialize a singleton.'); }

    public static function getInstance(): self
    {
        return self::$instance ??= new self();
    }

    public function pdo(): PDO { return $this->pdo; }

    /** Run a prepared statement and return it. */
    public function query(string $sql, array $params = []): PDOStatement
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function lastInsertId(): int { return (int)$this->pdo->lastInsertId(); }
}

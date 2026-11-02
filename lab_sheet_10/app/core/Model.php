<?php
/** Q5: base model with reusable CRUD. Child classes set $table and $fillable (column whitelist). */
abstract class Model
{
    protected static string $table;
    protected static array $fillable = [];

    protected static function db(): Database { return Database::getInstance(); }

    public static function all(int $limit = 100, int $offset = 0): array
    {
        $t = static::$table;
        return self::db()->query("SELECT * FROM `$t` ORDER BY id DESC LIMIT $limit OFFSET $offset")->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $t = static::$table;
        return self::db()->query("SELECT * FROM `$t` WHERE id = ?", [$id])->fetch() ?: null;
    }

    public static function count(): int
    {
        $t = static::$table;
        return (int)self::db()->query("SELECT COUNT(*) FROM `$t`")->fetchColumn();
    }

    public static function create(array $data): int
    {
        $data = array_intersect_key($data, array_flip(static::$fillable));
        $cols = implode(',', array_map(fn($c) => "`$c`", array_keys($data)));
        $marks = implode(',', array_fill(0, count($data), '?'));
        $t = static::$table;
        self::db()->query("INSERT INTO `$t` ($cols) VALUES ($marks)", array_values($data));
        return self::db()->lastInsertId();
    }

    public static function update(int $id, array $data): bool
    {
        $data = array_intersect_key($data, array_flip(static::$fillable));
        if (!$data) return false;
        $set = implode(',', array_map(fn($c) => "`$c` = ?", array_keys($data)));
        $t = static::$table;
        return self::db()->query("UPDATE `$t` SET $set WHERE id = ?", [...array_values($data), $id])->rowCount() >= 0;
    }

    public static function delete(int $id): bool
    {
        $t = static::$table;
        return self::db()->query("DELETE FROM `$t` WHERE id = ?", [$id])->rowCount() > 0;
    }
}

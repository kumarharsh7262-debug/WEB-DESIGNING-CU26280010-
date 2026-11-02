<?php
class Product extends Model
{
    protected static string $table = 'products';
    protected static array $fillable = ['name', 'category', 'price', 'stock', 'supplier_url', 'description', 'image'];

    /** Search + pagination. Returns [rows, total]. */
    public static function paginate(string $q, int $page, int $perPage): array
    {
        $where  = $q !== '' ? 'WHERE name LIKE ? OR category LIKE ?' : '';
        $params = $q !== '' ? ["%$q%", "%$q%"] : [];

        $total = (int)self::db()->query("SELECT COUNT(*) FROM products $where", $params)->fetchColumn();
        $offset = ($page - 1) * $perPage;
        $rows = self::db()->query(
            "SELECT * FROM products $where ORDER BY id DESC LIMIT $perPage OFFSET $offset", $params
        )->fetchAll();
        return [$rows, $total];
    }

    /** Q11: shared validation for the web form and the API. Returns [cleanData, errors]. */
    public static function validate(array $in): array
    {
        $errors = [];
        $clean = [];

        $clean['name'] = Validator::string($in['name'] ?? '', 1, 100);
        if ($clean['name'] === null) $errors['name'] = 'Name is required (max 100 characters).';

        $clean['category'] = Validator::string($in['category'] ?? '', 1, 50);
        if ($clean['category'] === null) $errors['category'] = 'Category is required (max 50 characters).';

        $clean['price'] = Validator::float($in['price'] ?? null, 0);
        if ($clean['price'] === null) $errors['price'] = 'Price must be a number >= 0.';

        $clean['stock'] = Validator::int($in['stock'] ?? 0, 0, 1000000);
        if ($clean['stock'] === null) $errors['stock'] = 'Stock must be a whole number >= 0.';

        $url = trim((string)($in['supplier_url'] ?? ''));
        if ($url === '') {
            $clean['supplier_url'] = null;
        } elseif (($clean['supplier_url'] = Validator::url($url)) === null) {
            $errors['supplier_url'] = 'Supplier URL must be a valid http(s) URL.';
        }

        $clean['description'] = Validator::string($in['description'] ?? '', 0, 1000);
        if ($clean['description'] === null) $errors['description'] = 'Description is too long (max 1000).';

        return [$clean, $errors];
    }
}

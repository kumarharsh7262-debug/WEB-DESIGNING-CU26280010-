<?php
/** Q7 + Q8: RESTful JSON API for products, protected by bearer-token authentication. */
class ApiController extends Controller
{
    /* POST /api/login  {email, password}  ->  {token, expires_at} */
    public function login(): void
    {
        $in    = $this->input();
        $email = Validator::email($in['email'] ?? '');
        $user  = $email ? User::findByEmail($email) : null;

        if ($user && User::lockMinutesLeft($user) > 0) $this->json(['error' => 'Account temporarily locked'], 429);
        if (!$user || !password_verify((string)($in['password'] ?? ''), $user['password_hash'])) {
            if ($user) User::recordFailure($user);
            $this->json(['error' => 'Invalid credentials'], 401);
        }
        User::clearFailures((int)$user['id']);
        [$token, $expires] = User::createApiToken((int)$user['id']);
        $this->json(['token' => $token, 'token_type' => 'Bearer', 'expires_at' => $expires]);
    }

    /* GET /api/products?page=1&q=term */
    public function index(): void
    {
        $this->authenticate();
        $q    = Validator::string($_GET['q'] ?? '', 0, 100) ?? '';
        $page = Validator::int($_GET['page'] ?? 1, 1) ?? 1;
        [$rows, $total] = Product::paginate($q, $page, 10);
        $this->json(['data' => $rows, 'meta' => ['page' => $page, 'per_page' => 10, 'total' => $total]]);
    }

    /* GET /api/products/{id} */
    public function show(string $id): void
    {
        $this->authenticate();
        $this->json(['data' => $this->findOr404($id)]);
    }

    /* POST /api/products */
    public function store(): void
    {
        $this->authenticate();
        [$data, $errors] = Product::validate($this->input());
        if ($errors) $this->json(['error' => 'Validation failed', 'fields' => $errors], 422);
        $newId = Product::create($data);
        header('Location: /api/products/' . $newId);
        $this->json(['data' => Product::find($newId)], 201);
    }

    /* PUT /api/products/{id} */
    public function update(string $id): void
    {
        $this->authenticate();
        $existing = $this->findOr404($id);
        [$data, $errors] = Product::validate(array_merge($existing, $this->input()));   // missing fields keep old value
        if ($errors) $this->json(['error' => 'Validation failed', 'fields' => $errors], 422);
        Product::update((int)$id, $data);
        $this->json(['data' => Product::find((int)$id)]);
    }

    /* DELETE /api/products/{id} */
    public function destroy(string $id): void
    {
        $this->authenticate();
        $p = $this->findOr404($id);
        Product::delete((int)$id);
        Uploader::delete($p['image']);
        $this->json(null, 204);
    }

    /* ---------- helpers ---------- */
    private function input(): array
    {
        $data = json_decode(file_get_contents('php://input') ?: '', true);
        return is_array($data) ? $data : [];
    }

    private function findOr404(string $id): array
    {
        $row = Validator::int($id, 1) ? Product::find((int)$id) : null;
        return $row ?? $this->json(['error' => 'Product not found'], 404);
    }

    /** Q8: require "Authorization: Bearer <token>". */
    private function authenticate(): array
    {
        $h = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
        if (!preg_match('/^Bearer\s+([a-f0-9]{64})$/i', $h, $m) || !($user = User::findByApiToken($m[1]))) {
            header('WWW-Authenticate: Bearer');
            $this->json(['error' => 'Unauthorized'], 401);
        }
        return $user;
    }
}

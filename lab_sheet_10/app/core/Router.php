<?php
/** Q4: tiny router - maps METHOD + URL pattern to "Controller@action". */
class Router
{
    private array $routes = [];

    public function get(string $p, string $h): void    { $this->add('GET', $p, $h); }
    public function post(string $p, string $h): void   { $this->add('POST', $p, $h); }
    public function put(string $p, string $h): void    { $this->add('PUT', $p, $h); }
    public function delete(string $p, string $h): void { $this->add('DELETE', $p, $h); }

    public function add(string $method, string $pattern, string $handler): void
    {
        $regex = '#^' . preg_replace('#\{(\w+)\}#', '(?P<$1>[^/]+)', rtrim($pattern, '/')) . '/?$#';
        $this->routes[] = compact('method', 'regex', 'handler');
    }

    public function dispatch(string $method, string $uri): void
    {
        $uri = '/' . trim(rawurldecode($uri), '/');
        $pathMatched = false;

        foreach ($this->routes as $route) {
            if (!preg_match($route['regex'], $uri, $m)) continue;
            $pathMatched = true;
            if ($route['method'] !== $method) continue;

            $params = array_filter($m, 'is_string', ARRAY_FILTER_USE_KEY);
            [$class, $action] = explode('@', $route['handler']);
            (new $class())->$action(...array_values($params));
            return;
        }

        $isApi = str_starts_with($uri, '/api');
        if ($pathMatched) {
            http_response_code(405);
            if ($isApi) { header('Content-Type: application/json'); echo json_encode(['error' => 'Method not allowed']); return; }
        }
        (new ErrorController())->notFound();
    }
}

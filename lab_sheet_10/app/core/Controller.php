<?php
/** Base controller: rendering views (Q6), JSON, redirects, CSRF + auth guards. */
abstract class Controller
{
    /** Q6: render a view wrapped in the shared header/nav/footer and pass data to it. */
    protected function render(string $view, array $data = [], int $status = 200): void
    {
        http_response_code($status);
        extract($data, EXTR_SKIP);
        $title = $data['title'] ?? config('app.name');
        require ROOT . '/app/views/partials/header.php';
        require ROOT . "/app/views/$view.php";
        require ROOT . '/app/views/partials/footer.php';
    }

    protected function json($data, int $status = 200): never
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        if ($status !== 204) echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    protected function redirect(string $path): never
    {
        header('Location: ' . url($path));
        exit;
    }

    protected function requireLogin(): void
    {
        if (!Session::get('user_id')) {
            Session::flash('error', 'Please log in first.');
            $this->redirect('/login');
        }
    }

    /** Q9: every state-changing web form calls this. */
    protected function verifyCsrf(): void
    {
        if (!Csrf::validate($_POST['_csrf'] ?? null)) {
            $this->render('errors/csrf', ['title' => 'Invalid request'], 419);
            exit;
        }
    }
}

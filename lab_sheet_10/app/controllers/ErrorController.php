<?php
/** Q16: custom error pages. */
class ErrorController extends Controller
{
    public function notFound(): void
    {
        if (str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/api')) {
            $this->json(['error' => 'Not found'], 404);
        }
        $this->render('errors/404', ['title' => 'Page not found'], 404);
    }
}

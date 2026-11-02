<?php
/** Q15: files live outside public/, so PHP streams them (with a strict whitelist). */
class UploadController extends Controller
{
    private const TYPES = ['jpg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif'];

    public function show(string $file): void
    {
        $path = Uploader::path($file);
        if (!$path) { (new ErrorController())->notFound(); return; }

        header('Content-Type: ' . self::TYPES[pathinfo($path, PATHINFO_EXTENSION)]);
        header('Content-Length: ' . filesize($path));
        header('X-Content-Type-Options: nosniff');
        header('Cache-Control: public, max-age=86400');
        readfile($path);
    }
}

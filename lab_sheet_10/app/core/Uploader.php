<?php
/** Q15: secure upload - checks MIME + size, renames, stores OUTSIDE the public web root. */
class Uploader
{
    /** @return array [storedName|null, error|null] */
    public static function save(array $file): array
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) return [null, null];   // optional
        if ($file['error'] !== UPLOAD_ERR_OK)                  return [null, 'Upload failed (code ' . $file['error'] . ').'];
        if (!is_uploaded_file($file['tmp_name']))              return [null, 'Invalid upload.'];
        if ($file['size'] > config('upload.max_size'))         return [null, 'File is larger than 2 MB.'];

        $mime    = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);   // real content, not client header
        $allowed = config('upload.allowed');
        if (!isset($allowed[$mime]) || @getimagesize($file['tmp_name']) === false) {
            return [null, 'Only JPG, PNG or GIF images are allowed.'];
        }

        $dir = config('paths.uploads');
        if (!is_dir($dir)) mkdir($dir, 0750, true);

        $name = bin2hex(random_bytes(16)) . '.' . $allowed[$mime];   // never trust the original name
        if (!move_uploaded_file($file['tmp_name'], "$dir/$name")) return [null, 'Could not save file.'];
        return [$name, null];
    }

    /** Safe absolute path for a stored file name, or null. */
    public static function path(string $name): ?string
    {
        if (!preg_match('/^[a-f0-9]{32}\.(jpg|png|gif)$/', $name)) return null;
        $p = config('paths.uploads') . '/' . $name;
        return is_file($p) ? $p : null;
    }

    public static function delete(?string $name): void
    {
        if ($name && ($p = self::path($name))) @unlink($p);
    }
}

<?php
// env.php — loads key=value pairs from .env into environment variables

$envPath = __DIR__ . '/.env';
if (!file_exists($envPath)) {
    return;
}

$lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

foreach ($lines as $line) {
    if (str_starts_with(trim($line), '#')) {
        continue; // skip comments
    }

    list($name, $value) = array_map('trim', explode('=', $line, 2));
    if (!getenv($name)) {
        putenv("$name=$value");
        $_ENV[$name] = $value;
    }
}
?>

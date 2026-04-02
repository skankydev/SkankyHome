<?php
/**
 * Bootstrap — chargé avant tout démarrage (web et CLI)
 *
 * Responsabilités :
 * - Chargement du .env
 * - Initialisation de la locale
 */

// Chargement du .env
(function () {
    $envFile = APP_FOLDER . DS . '.env';
    if (!file_exists($envFile)) return;
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (str_starts_with(trim($line), '#')) continue;
        [$key, $value] = explode('=', $line, 2) + [1 => ''];
        $_ENV[trim($key)] = trim($value);
        putenv(trim($key) . '=' . trim($value));
    }
})();

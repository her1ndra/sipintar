<?php
/**
 * Router untuk PHP built-in server (pengembangan):
 *     php -S localhost:8000 router.php
 * Built-in server mengabaikan .htaccess, jadi folder sensitif diblokir di sini.
 */
$uri  = rawurldecode((string) parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH));
$uri  = '/' . ltrim(str_replace('\\', '/', $uri), '/');

$diblokir = '#^/(?:database|vendor|includes|config|tests|uploads|assets/uploads)(?:/|$)#i';
$fileSensitif = '#(?:\.(?:sql|md|lock|log|bak|env)$|/composer\.json$|/\.(?:git|htaccess|gitignore))#i';

if (strpos($uri, '..') !== false || preg_match($diblokir, $uri) || preg_match($fileSensitif, $uri)) {
    http_response_code(403);
    header('Content-Type: text/plain; charset=utf-8');
    echo 'Forbidden';
    return true;
}
return false;

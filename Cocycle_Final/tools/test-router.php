<?php
// Loopback-only test server; uses the database created by setup-browser-test.php.
$runtime = json_decode(file_get_contents(__DIR__.'/../storage/testing/runtime.json'), true);
if (!preg_match('/^cocycle_test_[a-zA-Z0-9_]+$/', $runtime['database'])) throw new RuntimeException('Unsafe database.');
putenv('DB_DATABASE='.$runtime['database']);
$_ENV['DB_DATABASE'] = $_SERVER['DB_DATABASE'] = $runtime['database'];
putenv('APP_ENV=local');
$_ENV['APP_ENV'] = $_SERVER['APP_ENV'] = 'local';
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$file = realpath(__DIR__.'/../public'.$uri);
$public = realpath(__DIR__.'/../public');
if ($uri !== '/' && $file && str_starts_with($file, $public.DIRECTORY_SEPARATOR) && is_file($file) && pathinfo($file, PATHINFO_EXTENSION) !== 'php') return false;
require __DIR__.'/../public/index.php';

<?php

function h(?string $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function url(string $path = ''): string
{
    $path = '/' . ltrim($path, '/');
    return $path === '/' ? '/' : $path;
}

function redirect(string $path): never
{
    header('Location: ' . url($path));
    exit;
}

function badge_access(string $level): string
{
    return match ($level) {
        'paid', '付费' => '付费',
        'login', '登录' => '登录',
        'free', '免费', 'public_free', '公开/免费' => '免费',
        default => $level,
    };
}

function json_output(array $payload, int $statusCode = 200): never
{
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    exit;
}

function request_data(): array
{
    $data = array_merge($_GET, $_POST);
    $raw = file_get_contents('php://input');
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

    if ($raw !== '' && str_contains($contentType, 'application/json')) {
        $json = json_decode($raw, true);
        if (is_array($json)) {
            $data = array_merge($data, $json);
        }
    }

    unset($data['route']);
    return $data;
}

function normalize_route(string $route): string
{
    $route = trim($route);
    $route = trim($route, '/');
    return strtolower($route);
}

function scan_api_scripts(): array
{
    $dir = realpath(__DIR__ . '/../api');
    if (!$dir) {
        return [];
    }

    $files = glob($dir . '/*.php') ?: [];
    return array_map('basename', $files);
}

function handle_api_script_upload(?array $file, string $route, string $currentScript = ''): string
{
    if (!$file || (int)($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return basename($currentScript);
    }

    if ((int)$file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('脚本文件上传失败，请重新选择文件');
    }

    $originalName = basename((string)($file['name'] ?? ''));
    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

    if ($extension !== 'php') {
        throw new RuntimeException('脚本文件只允许上传 .php 文件');
    }

    $safeRoute = normalize_route($route);
    $baseName = $safeRoute !== '' ? $safeRoute . '.php' : preg_replace('/[^a-zA-Z0-9_.-]/', '_', $originalName);
    $targetDir = realpath(__DIR__ . '/../api');

    if (!$targetDir) {
        throw new RuntimeException('api目录不存在');
    }

    $targetPath = $targetDir . DIRECTORY_SEPARATOR . $baseName;

    if (!move_uploaded_file((string)$file['tmp_name'], $targetPath)) {
        throw new RuntimeException('脚本文件保存失败，请检查api目录权限');
    }

    return $baseName;
}

function csrf_token(): string
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
    }

    return $_SESSION['csrf_token'];
}

function verify_csrf(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }

    $token = $_POST['_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(419);
        exit('CSRF token mismatch');
    }
}

function format_count(int $num): string
{
    if ($num >= 1000000) {
        return rtrim(rtrim((string)round($num / 1000000, 1), '0'), '.') . 'M';
    }
    if ($num >= 1000) {
        return rtrim(rtrim((string)round($num / 1000, 1), '0'), '.') . 'K';
    }
    return (string)$num;
}

function icon_svg(?string $icon, string $name = 'API'): string
{
    $icon = trim((string)$icon);
    $label = trim($name) !== '' ? trim($name) : 'API';

    if ($icon !== '' && preg_match('/^(https?:\/\/|\/|\.\/|\.\.\/|assets\/|uploads\/)/i', $icon)) {
        return '<span class="api-icon api-icon-img"><img src="' . h($icon) . '" alt="' . h($label) . '"></span>';
    }

    $map = [
        'netease' => ['#ee4d67', '♪'],
        'ip' => ['#2b8edc', 'IP'],
        'json' => ['#22a06b', '{}'],
        'weather' => ['#5b8def', '☁'],
        'doubao' => ['#f0a2a8', '豆'],
        'bilibili' => ['#00a1d6', 'B'],
        'douyin' => ['#111827', '♪'],
        'clock' => ['#6b7280', '时'],
        'api' => ['#0ea5e9', 'API'],
    ];

    if ($icon !== '' && isset($map[$icon])) {
        [$bg, $text] = $map[$icon];
        return '<span class="api-icon" style="background:' . h($bg) . '">' . h($text) . '</span>';
    }

    if ($icon !== '') {
        $text = function_exists('mb_substr') ? mb_substr($icon, 0, 2, 'UTF-8') : substr($icon, 0, 2);
        return '<span class="api-icon" style="background:#667eea">' . h($text) . '</span>';
    }

    $first = function_exists('mb_substr') ? mb_substr($label, 0, 1, 'UTF-8') : substr($label, 0, 1);
    return '<span class="api-icon" style="background:#667eea">' . h($first ?: 'A') . '</span>';
}


function excerpt(?string $value, int $limit = 88): string
{
    $value = trim((string)$value);
    if (function_exists('mb_strimwidth')) {
        return mb_strimwidth($value, 0, $limit, '...', 'UTF-8');
    }
    return strlen($value) > $limit ? substr($value, 0, $limit) . '...' : $value;
}

function lower_text(string $value): string
{
    return function_exists('mb_strtolower') ? mb_strtolower($value, 'UTF-8') : strtolower($value);
}

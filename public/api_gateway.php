<?php

require_once __DIR__ . '/../app/db.php';
require_once __DIR__ . '/../app/helpers.php';

$started = microtime(true);
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '';
$route = $_GET['route'] ?? '';

if ($route === '' && preg_match('#^/api/([a-zA-Z0-9_-]+)$#', rtrim($uri, '/'), $matches)) {
    $route = $matches[1];
}

$route = normalize_route($route);

if (!preg_match('/^[a-z0-9_-]+$/', $route)) {
    json_output(['code' => 400, 'msg' => 'Invalid route', 'data' => null], 400);
}

$database = db();
$api = $database->getApiByRoute($route, true);

if (!$api) {
    json_output(['code' => 404, 'msg' => 'API not found or unpublished', 'data' => null], 404);
}

$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
$allowed = array_map('trim', explode(',', strtoupper($api['method_set'])));

if (!in_array($method, $allowed, true)) {
    json_output(['code' => 405, 'msg' => 'Method not allowed', 'data' => ['allowed' => $allowed]], 405);
}

$input = request_data();

if ((int)($api['require_key'] ?? 0) === 1) {
    $apiKey = trim((string)($input['key'] ?? ''));
    if ($apiKey === '') {
        log_call_file($database, $api, 401, $started);
        json_output(['code' => 401, 'msg' => 'Missing API key', 'data' => null], 401);
    }

    if (!$database->activeApiKey($apiKey)) {
        log_call_file($database, $api, 403, $started);
        json_output(['code' => 403, 'msg' => 'Invalid or inactive API key', 'data' => null], 403);
    }
}

$params = $database->paramsForApi((int)$api['id']);

foreach ($params as $param) {
    $name = $param['param_name'];
    if ((int)$param['required'] === 1 && (!array_key_exists($name, $input) || $input[$name] === '')) {
        log_call_file($database, $api, 422, $started);
        json_output(['code' => 422, 'msg' => "Missing required parameter: {$name}", 'data' => null], 422);
    }
}

$apiDir = realpath(__DIR__ . '/../api');
$scriptFile = basename($api['script_file']);
$scriptPath = realpath($apiDir . DIRECTORY_SEPARATOR . $scriptFile);

if (!$scriptPath || !str_starts_with($scriptPath, $apiDir . DIRECTORY_SEPARATOR) || !is_file($scriptPath)) {
    log_call_file($database, $api, 500, $started);
    json_output(['code' => 500, 'msg' => 'API script not found', 'data' => null], 500);
}

try {
    $handler = require $scriptPath;

    if (!is_callable($handler)) {
        throw new RuntimeException('API script must return callable');
    }

    $data = $handler($input, [
        'route' => $route,
        'method' => $method,
        'api' => $api,
    ]);

    log_call_file($database, $api, 200, $started, true);

    json_output([
        'code' => 0,
        'msg' => 'success',
        'data' => $data,
        'meta' => [
            'route' => $route,
            'latency_ms' => (int)((microtime(true) - $started) * 1000),
        ],
    ]);
} catch (Throwable $e) {
    log_call_file($database, $api, 500, $started);
    json_output(['code' => 500, 'msg' => $e->getMessage(), 'data' => null], 500);
}

function log_call_file(FileDatabase $database, array $api, int $statusCode, float $started, bool $success = false): void
{
    $latencyMs = (int)((microtime(true) - $started) * 1000);
    $database->logCall($api, $statusCode, $latencyMs, $success);
}

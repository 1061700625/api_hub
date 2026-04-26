<?php

require __DIR__ . '/../app/db.php';
require __DIR__ . '/../app/helpers.php';
require __DIR__ . '/../app/auth.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';
$uri = rtrim($uri, '/') ?: '/';
$method = $_SERVER['REQUEST_METHOD'];

if (str_starts_with($uri, '/api/') && $uri !== '/api-doc') {
    require __DIR__ . '/api_gateway.php';
    exit;
}

if ($uri === '/') {
    front_list();
    exit;
}

if (preg_match('#^/api-doc/([a-zA-Z0-9_-]+)$#', $uri, $matches)) {
    front_doc($matches[1]);
    exit;
}

if ($uri === '/admin') {
    redirect('/admin/apis');
}

if ($uri === '/admin/login') {
    admin_login();
    exit;
}

if ($uri === '/admin/logout') {
    logout_admin();
    redirect('/admin/login');
}

if ($uri === '/admin/apis') {
    require_admin();
    admin_apis();
    exit;
}

if ($uri === '/admin/apis/create') {
    require_admin();
    admin_api_form();
    exit;
}

if ($uri === '/admin/apis/edit') {
    require_admin();
    admin_api_form((int)($_GET['id'] ?? 0));
    exit;
}

if ($uri === '/admin/apis/disable') {
    require_admin();
    admin_api_disable((int)($_GET['id'] ?? 0));
    exit;
}

if ($uri === '/admin/apis/toggle') {
    require_admin();
    admin_api_toggle((int)($_GET['id'] ?? 0));
    exit;
}

if ($uri === '/admin/apis/delete') {
    require_admin();
    admin_api_delete((int)($_GET['id'] ?? 0));
    exit;
}

http_response_code(404);
echo '404 Not Found';

function front_list(): void
{
    $database = db();
    $keyword = trim($_GET['keyword'] ?? '');
    $category = trim($_GET['category'] ?? '');
    $apis = $database->publishedApis($keyword, $category);
    $categories = $database->categories();

    $title = 'API 接口列表';
    include __DIR__ . '/../views/front/list.php';
}

function front_doc(string $route): void
{
    $database = db();
    $api = $database->getApiByRoute($route, true);

    if (!$api) {
        http_response_code(404);
        echo 'API not found';
        return;
    }

    $params = $database->paramsForApi((int)$api['id']);
    $responseParams = $database->responseParamsForApi((int)$api['id']);

    $title = $api['name'];
    include __DIR__ . '/../views/front/doc.php';
}

function admin_login(): void
{
    start_session();
    $error = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        verify_csrf();
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (login_admin($username, $password)) {
            redirect('/admin/apis');
        }

        $error = '用户名或密码错误';
    }

    $title = '管理员登录';
    include __DIR__ . '/../views/admin/login.php';
}

function admin_apis(): void
{
    $apis = db()->allApis();
    $admin = current_admin();
    $title = '后台管理';
    include __DIR__ . '/../views/admin/apis.php';
}

function admin_api_form(int $id = 0): void
{
    $database = db();
    start_session();

    $api = [
        'id' => 0,
        'category_id' => '',
        'name' => '',
        'route' => '',
        'script_file' => '',
        'icon' => '',
        'description' => '',
        'method_set' => 'GET,POST',
        'response_format' => 'JSON',
        'access_level' => '免费',
        'status' => 'draft',
    ];
    $params = [];
    $responseParams = [];
    $error = '';

    if ($id > 0) {
        $found = $database->getApiById($id);
        if (!$found) {
            http_response_code(404);
            echo 'API not found';
            return;
        }
        $api = $found;
        $params = $database->paramsForApi($id);
        $responseParams = $database->responseParamsForApi($id);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        verify_csrf();

        $api['name'] = trim($_POST['name'] ?? '');
        $api['route'] = normalize_route($_POST['route'] ?? '');

        try {
            $newCategoryName = trim($_POST['new_category_name'] ?? '');
            if ($newCategoryName !== '') {
                $api['category_id'] = $database->addCategory($newCategoryName);
            } else {
                $api['category_id'] = ($_POST['category_id'] ?? '') !== '' ? (int)$_POST['category_id'] : null;
            }

            $api['script_file'] = handle_api_script_upload($_FILES['script_upload'] ?? null, $api['route'], $api['script_file'] ?? '');
        } catch (Throwable $e) {
            $error = $e->getMessage();
        }

        $api['icon'] = trim($_POST['icon'] ?? '');
        $api['description'] = trim($_POST['description'] ?? '');
        $api['method_set'] = implode(',', $_POST['methods'] ?? ['GET']);
        $api['response_format'] = 'JSON';
        $api['access_level'] = trim($_POST['access_level'] ?? '免费');
        $api['status'] = trim($_POST['status'] ?? 'draft');

        $postedParams = $_POST['params'] ?? [];
        $params = [];
        foreach ($postedParams as $index => $row) {
            $name = trim($row['param_name'] ?? '');
            if ($name === '') {
                continue;
            }
            $params[] = [
                'param_name' => $name,
                'required' => !empty($row['required']) ? 1 : 0,
                'param_type' => trim($row['param_type'] ?? 'string'),
                'description' => trim($row['description'] ?? ''),
                'example_value' => trim($row['example_value'] ?? ''),
                'sort_order' => $index,
            ];
        }

        $postedResponseParams = $_POST['response_params'] ?? [];
        $responseParams = [];
        foreach ($postedResponseParams as $index => $row) {
            $name = trim($row['param_name'] ?? '');
            if ($name === '') {
                continue;
            }
            $responseParams[] = [
                'param_name' => $name,
                'param_type' => trim($row['param_type'] ?? 'string'),
                'description' => trim($row['description'] ?? ''),
                'example_value' => trim($row['example_value'] ?? ''),
                'sort_order' => $index,
            ];
        }

        if ($error !== '') {
            // 保留上传或分类处理阶段的错误提示
        } elseif ($api['name'] === '' || $api['route'] === '' || $api['script_file'] === '') {
            $error = 'API名称、路由和脚本文件必填';
        } elseif (!preg_match('/^[a-z0-9_-]+$/', $api['route'])) {
            $error = '路由只能包含小写字母、数字、下划线和中划线';
        } elseif (!is_file(__DIR__ . '/../api/' . $api['script_file'])) {
            $error = 'api目录下找不到该脚本文件';
        } else {
            try {
                $database->saveApi($api, $params, $responseParams, $id);
                redirect('/admin/apis');
            } catch (Throwable $e) {
                $error = $e->getMessage();
            }
        }
    }

    $categories = $database->categories();
    $scripts = scan_api_scripts();
    $admin = current_admin();
    $title = $id > 0 ? '编辑API' : '新增API';
    include __DIR__ . '/../views/admin/api_form.php';
}

function admin_api_toggle(int $id): void
{
    if ($id > 0) {
        db()->toggleApi($id);
    }
    redirect('/admin/apis');
}

function admin_api_disable(int $id): void
{
    if ($id > 0) {
        db()->disableApi($id);
    }
    redirect('/admin/apis');
}

function admin_api_delete(int $id): void
{
    if ($id > 0) {
        db()->deleteApi($id);
    }
    redirect('/admin/apis');
}

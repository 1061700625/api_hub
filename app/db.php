<?php

class FileDatabase
{
    private string $file;
    private array $data = [];

    public function __construct(string $file)
    {
        $this->file = $file;
        $dir = dirname($file);
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
        $this->load();
        $this->ensureDataShape();
        $this->ensureDefaultAdmin();
    }

    private function load(): void
    {
        if (!is_file($this->file)) {
            $this->data = $this->seedData();
            $this->save();
            return;
        }

        $json = file_get_contents($this->file);
        $data = json_decode($json, true);
        if (!is_array($data)) {
            $this->data = $this->seedData();
            $this->save();
            return;
        }
        $this->data = $data;
    }

    public function save(): void
    {
        file_put_contents($this->file, json_encode($this->data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    }

    public function reset(): void
    {
        $this->data = $this->seedData();
        $this->ensureDataShape();
        $this->ensureDefaultAdmin();
        $this->save();
    }

    private function seedData(): array
    {
        return [
            'admins' => [],
            'api_categories' => [
                ['id' => 1, 'name' => '解析类', 'sort_order' => 1],
                ['id' => 2, 'name' => '查询类', 'sort_order' => 2],
                ['id' => 3, 'name' => '工具类', 'sort_order' => 3],
                ['id' => 4, 'name' => '图片类', 'sort_order' => 4],
            ],
            'apis' => [
                ['id'=>1,'category_id'=>1,'name'=>'网易云音乐SVIP解析','route'=>'163_music','script_file'=>'163_music.php','icon'=>'netease','description'=>'网易云音乐解析，演示接口会返回歌曲基础信息和播放地址。','method_set'=>'GET,POST','response_format'=>'JSON','access_level'=>'免费','status'=>'published','call_count'=>2133611,'success_count'=>2133611,'avg_latency_ms'=>215,'created_at'=>'2026-04-27 10:00:00','updated_at'=>'2026-04-27 10:00:00'],
                ['id'=>2,'category_id'=>2,'name'=>'IP归属地查询','route'=>'ip_query','script_file'=>'ip_query.php','icon'=>'ip','description'=>'查询IP地理位置及归属地信息，不传IP时默认使用当前访问IP。','method_set'=>'GET,POST','response_format'=>'JSON','access_level'=>'免费','status'=>'published','call_count'=>3100000,'success_count'=>3098800,'avg_latency_ms'=>38,'created_at'=>'2026-04-25 10:00:00','updated_at'=>'2026-04-25 10:00:00'],
                ['id'=>3,'category_id'=>3,'name'=>'JSON回显测试','route'=>'echo_json','script_file'=>'echo_json.php','icon'=>'json','description'=>'用于测试GET/POST参数是否正常进入API网关。','method_set'=>'GET,POST','response_format'=>'JSON','access_level'=>'免费','status'=>'published','call_count'=>12000,'success_count'=>12000,'avg_latency_ms'=>9,'created_at'=>'2026-04-26 10:00:00','updated_at'=>'2026-04-26 10:00:00'],
                ['id'=>4,'category_id'=>2,'name'=>'城市天气id','route'=>'weather_city_id','script_file'=>'weather_city_id.php','icon'=>'weather','description'=>'查询演示城市对应的天气ID。','method_set'=>'GET,POST','response_format'=>'JSON','access_level'=>'免费','status'=>'published','call_count'=>1400,'success_count'=>1398,'avg_latency_ms'=>42,'created_at'=>'2026-04-26 10:00:00','updated_at'=>'2026-04-26 10:00:00'],
                ['id'=>5,'category_id'=>4,'name'=>'豆包视频解析','route'=>'doubao_video','script_file'=>'doubao_video.php','icon'=>'doubao','description'=>'豆包短视频解析演示接口。','method_set'=>'GET,POST','response_format'=>'JSON','access_level'=>'免费','status'=>'published','call_count'=>2100,'success_count'=>2098,'avg_latency_ms'=>63,'created_at'=>'2026-04-26 10:00:00','updated_at'=>'2026-04-26 10:00:00'],
                ['id'=>6,'category_id'=>1,'name'=>'bilibili解析','route'=>'bilibili','script_file'=>'bilibili.php','icon'=>'bilibili','description'=>'只能解析bilibili短视频，画质为1080，支持合集演示。','method_set'=>'GET,POST','response_format'=>'JSON','access_level'=>'免费','status'=>'published','call_count'=>1700000,'success_count'=>1698123,'avg_latency_ms'=>89,'created_at'=>'2026-04-27 10:00:00','updated_at'=>'2026-04-27 10:00:00'],
                ['id'=>7,'category_id'=>1,'name'=>'抖音无水印解析','route'=>'douyin_no_watermark','script_file'=>'douyin_no_watermark.php','icon'=>'douyin','description'=>'抖音去水印解析，支持图文和短视频解析。','method_set'=>'GET,POST','response_format'=>'JSON','access_level'=>'免费','status'=>'published','call_count'=>1600000,'success_count'=>1599012,'avg_latency_ms'=>102,'created_at'=>'2026-04-27 10:00:00','updated_at'=>'2026-04-27 10:00:00'],
                ['id'=>8,'category_id'=>3,'name'=>'时间戳转换','route'=>'timestamp','script_file'=>'timestamp.php','icon'=>'clock','description'=>'时间戳与日期字符串互转。','method_set'=>'GET,POST','response_format'=>'JSON','access_level'=>'免费','status'=>'published','call_count'=>9800,'success_count'=>9780,'avg_latency_ms'=>5,'created_at'=>'2026-04-27 10:00:00','updated_at'=>'2026-04-27 10:00:00'],
            ],
            'api_params' => [
                ['id'=>1,'api_id'=>1,'param_name'=>'id','required'=>0,'param_type'=>'string','description'=>'网易云音乐歌曲ID。id和url至少传一个。','example_value'=>'123456','sort_order'=>1],
                ['id'=>2,'api_id'=>1,'param_name'=>'url','required'=>0,'param_type'=>'string','description'=>'网易云音乐分享链接。id和url至少传一个。','example_value'=>'https://music.163.com/song?id=123456','sort_order'=>2],
                ['id'=>3,'api_id'=>2,'param_name'=>'ip','required'=>0,'param_type'=>'string','description'=>'要查询的IP地址，不传则默认当前访问IP。','example_value'=>'8.8.8.8','sort_order'=>1],
                ['id'=>4,'api_id'=>3,'param_name'=>'name','required'=>0,'param_type'=>'string','description'=>'任意字符串，用于回显测试。','example_value'=>'hello','sort_order'=>1],
                ['id'=>5,'api_id'=>4,'param_name'=>'city','required'=>1,'param_type'=>'string','description'=>'城市名称。','example_value'=>'北京','sort_order'=>1],
                ['id'=>6,'api_id'=>5,'param_name'=>'url','required'=>1,'param_type'=>'string','description'=>'豆包短视频链接。','example_value'=>'https://example.com/video/1','sort_order'=>1],
                ['id'=>7,'api_id'=>6,'param_name'=>'url','required'=>1,'param_type'=>'string','description'=>'bilibili视频链接。','example_value'=>'https://www.bilibili.com/video/BVxxxx','sort_order'=>1],
                ['id'=>8,'api_id'=>7,'param_name'=>'url','required'=>1,'param_type'=>'string','description'=>'抖音分享链接。','example_value'=>'https://v.douyin.com/xxxx/','sort_order'=>1],
                ['id'=>9,'api_id'=>8,'param_name'=>'timestamp','required'=>0,'param_type'=>'integer','description'=>'Unix时间戳。','example_value'=>'1735689600','sort_order'=>1],
                ['id'=>10,'api_id'=>8,'param_name'=>'datetime','required'=>0,'param_type'=>'string','description'=>'日期时间字符串。','example_value'=>'2025-01-01 00:00:00','sort_order'=>2],
            ],
            'api_response_params' => [
                ['id'=>1,'api_id'=>1,'param_name'=>'code','param_type'=>'integer','description'=>'业务状态码，0 表示请求成功。','example_value'=>'0','sort_order'=>1],
                ['id'=>2,'api_id'=>1,'param_name'=>'msg','param_type'=>'string','description'=>'接口调用结果说明。','example_value'=>'success','sort_order'=>2],
                ['id'=>3,'api_id'=>1,'param_name'=>'data','param_type'=>'object','description'=>'接口返回的业务数据。具体字段以对应 API 为准。','example_value'=>'{}','sort_order'=>3],
                ['id'=>4,'api_id'=>2,'param_name'=>'code','param_type'=>'integer','description'=>'业务状态码，0 表示请求成功。','example_value'=>'0','sort_order'=>1],
                ['id'=>5,'api_id'=>2,'param_name'=>'msg','param_type'=>'string','description'=>'接口调用结果说明。','example_value'=>'success','sort_order'=>2],
                ['id'=>6,'api_id'=>2,'param_name'=>'data','param_type'=>'object','description'=>'接口返回的业务数据。具体字段以对应 API 为准。','example_value'=>'{}','sort_order'=>3],
                ['id'=>7,'api_id'=>3,'param_name'=>'code','param_type'=>'integer','description'=>'业务状态码，0 表示请求成功。','example_value'=>'0','sort_order'=>1],
                ['id'=>8,'api_id'=>3,'param_name'=>'msg','param_type'=>'string','description'=>'接口调用结果说明。','example_value'=>'success','sort_order'=>2],
                ['id'=>9,'api_id'=>3,'param_name'=>'data','param_type'=>'object','description'=>'接口返回的业务数据。具体字段以对应 API 为准。','example_value'=>'{}','sort_order'=>3],
                ['id'=>10,'api_id'=>4,'param_name'=>'code','param_type'=>'integer','description'=>'业务状态码，0 表示请求成功。','example_value'=>'0','sort_order'=>1],
                ['id'=>11,'api_id'=>4,'param_name'=>'msg','param_type'=>'string','description'=>'接口调用结果说明。','example_value'=>'success','sort_order'=>2],
                ['id'=>12,'api_id'=>4,'param_name'=>'data','param_type'=>'object','description'=>'接口返回的业务数据。具体字段以对应 API 为准。','example_value'=>'{}','sort_order'=>3],
                ['id'=>13,'api_id'=>5,'param_name'=>'code','param_type'=>'integer','description'=>'业务状态码，0 表示请求成功。','example_value'=>'0','sort_order'=>1],
                ['id'=>14,'api_id'=>5,'param_name'=>'msg','param_type'=>'string','description'=>'接口调用结果说明。','example_value'=>'success','sort_order'=>2],
                ['id'=>15,'api_id'=>5,'param_name'=>'data','param_type'=>'object','description'=>'接口返回的业务数据。具体字段以对应 API 为准。','example_value'=>'{}','sort_order'=>3],
                ['id'=>16,'api_id'=>6,'param_name'=>'code','param_type'=>'integer','description'=>'业务状态码，0 表示请求成功。','example_value'=>'0','sort_order'=>1],
                ['id'=>17,'api_id'=>6,'param_name'=>'msg','param_type'=>'string','description'=>'接口调用结果说明。','example_value'=>'success','sort_order'=>2],
                ['id'=>18,'api_id'=>6,'param_name'=>'data','param_type'=>'object','description'=>'接口返回的业务数据。具体字段以对应 API 为准。','example_value'=>'{}','sort_order'=>3],
                ['id'=>19,'api_id'=>7,'param_name'=>'code','param_type'=>'integer','description'=>'业务状态码，0 表示请求成功。','example_value'=>'0','sort_order'=>1],
                ['id'=>20,'api_id'=>7,'param_name'=>'msg','param_type'=>'string','description'=>'接口调用结果说明。','example_value'=>'success','sort_order'=>2],
                ['id'=>21,'api_id'=>7,'param_name'=>'data','param_type'=>'object','description'=>'接口返回的业务数据。具体字段以对应 API 为准。','example_value'=>'{}','sort_order'=>3],
                ['id'=>22,'api_id'=>8,'param_name'=>'code','param_type'=>'integer','description'=>'业务状态码，0 表示请求成功。','example_value'=>'0','sort_order'=>1],
                ['id'=>23,'api_id'=>8,'param_name'=>'msg','param_type'=>'string','description'=>'接口调用结果说明。','example_value'=>'success','sort_order'=>2],
                ['id'=>24,'api_id'=>8,'param_name'=>'data','param_type'=>'object','description'=>'接口返回的业务数据。具体字段以对应 API 为准。','example_value'=>'{}','sort_order'=>3],
            ],
            'api_keys' => [],
            'api_call_logs' => [],
        ];
    }

    private function ensureDataShape(): void
    {
        $changed = false;

        foreach ([
            'admins',
            'api_categories',
            'apis',
            'api_params',
            'api_response_params',
            'api_keys',
            'api_call_logs',
        ] as $table) {
            if (!isset($this->data[$table]) || !is_array($this->data[$table])) {
                $this->data[$table] = [];
                $changed = true;
            }
        }

        foreach ($this->data['apis'] as $idx => $api) {
            if (!array_key_exists('require_key', $api)) {
                $this->data['apis'][$idx]['require_key'] = 0;
                $changed = true;
            }
        }

        foreach ($this->data['api_keys'] as $idx => $key) {
            foreach ([
                'status' => 'pending',
                'created_at' => $this->now(),
                'updated_at' => $this->now(),
                'approved_at' => null,
                'email' => '',
                'purpose' => '',
                'ip' => '',
                'user_agent' => '',
            ] as $field => $default) {
                if (!array_key_exists($field, $key)) {
                    $this->data['api_keys'][$idx][$field] = $default;
                    $changed = true;
                }
            }
        }

        if ($changed) {
            $this->save();
        }
    }

    private function nextId(string $table): int
    {
        $max = 0;
        foreach ($this->data[$table] ?? [] as $row) {
            $max = max($max, (int)$row['id']);
        }
        return $max + 1;
    }

    private function now(): string
    {
        return date('Y-m-d H:i:s');
    }

    private function uuidV4(): string
    {
        $data = random_bytes(16);
        $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
        $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    public function ensureDefaultAdmin(): void
    {
        $config = require __DIR__ . '/../config.php';
        $username = $config['admin']['default_username'];
        if ($this->getAdminByUsername($username)) {
            return;
        }
        $this->data['admins'][] = [
            'id' => $this->nextId('admins'),
            'username' => $username,
            'password_hash' => password_hash($config['admin']['default_password'], PASSWORD_DEFAULT),
            'created_at' => $this->now(),
        ];
        $this->save();
    }

    public function getAdminByUsername(string $username): ?array
    {
        foreach ($this->data['admins'] as $admin) {
            if ($admin['username'] === $username) {
                return $admin;
            }
        }
        return null;
    }

    public function getAdminById(int $id): ?array
    {
        foreach ($this->data['admins'] as $admin) {
            if ((int)$admin['id'] === $id) {
                return $admin;
            }
        }
        return null;
    }

    public function categories(): array
    {
        $rows = $this->data['api_categories'];
        usort($rows, fn($a, $b) => [$a['sort_order'], $a['id']] <=> [$b['sort_order'], $b['id']]);
        return $rows;
    }

    public function addCategory(string $name): int
    {
        $name = trim($name);
        if ($name === '') {
            throw new RuntimeException('分类名称不能为空');
        }

        foreach ($this->data['api_categories'] as $cat) {
            if ($cat['name'] === $name) {
                return (int)$cat['id'];
            }
        }

        $id = $this->nextId('api_categories');
        $this->data['api_categories'][] = [
            'id' => $id,
            'name' => $name,
            'sort_order' => $id,
        ];
        $this->save();

        return $id;
    }

    private function categoryName(?int $id): ?string
    {
        foreach ($this->data['api_categories'] as $cat) {
            if ((int)$cat['id'] === (int)$id) {
                return $cat['name'];
            }
        }
        return null;
    }

    private function todayCallCountForApi(int $apiId): int
    {
        $today = date('Y-m-d');
        $count = 0;

        foreach ($this->data['api_call_logs'] ?? [] as $log) {
            if ((int)($log['api_id'] ?? 0) !== $apiId) {
                continue;
            }

            if (str_starts_with((string)($log['created_at'] ?? ''), $today)) {
                $count++;
            }
        }

        return $count;
    }

    private function withCategory(array $api): array
    {
        $api['category_name'] = $this->categoryName($api['category_id'] ?? null);
        $api['today_call_count'] = $this->todayCallCountForApi((int)($api['id'] ?? 0));
        return $api;
    }

    public function publishedApis(string $keyword = '', string $category = ''): array
    {
        $rows = [];
        foreach ($this->data['apis'] as $api) {
            if (($api['status'] ?? '') !== 'published') {
                continue;
            }
            if ($keyword !== '') {
                $haystack = lower_text(($api['name'] ?? '') . ' ' . ($api['description'] ?? '') . ' ' . ($api['route'] ?? ''));
                if (!str_contains($haystack, lower_text($keyword))) {
                    continue;
                }
            }
            if ($category !== '' && (string)$api['category_id'] !== (string)$category) {
                continue;
            }
            $rows[] = $this->withCategory($api);
        }
        usort($rows, fn($a, $b) => strcmp($b['updated_at'], $a['updated_at']) ?: ((int)$b['id'] <=> (int)$a['id']));
        return $rows;
    }

    public function allApis(): array
    {
        $rows = array_map(fn($api) => $this->withCategory($api), $this->data['apis']);
        usort($rows, fn($a, $b) => (int)$a['id'] <=> (int)$b['id']);
        return $rows;
    }

    public function getApiByRoute(string $route, bool $publishedOnly = false): ?array
    {
        foreach ($this->data['apis'] as $api) {
            if ($api['route'] === $route && (!$publishedOnly || $api['status'] === 'published')) {
                return $this->withCategory($api);
            }
        }
        return null;
    }

    public function getApiById(int $id): ?array
    {
        foreach ($this->data['apis'] as $api) {
            if ((int)$api['id'] === $id) {
                return $this->withCategory($api);
            }
        }
        return null;
    }

    public function paramsForApi(int $apiId): array
    {
        $rows = [];
        foreach ($this->data['api_params'] as $row) {
            if ((int)$row['api_id'] === $apiId) {
                $rows[] = $row;
            }
        }
        usort($rows, fn($a, $b) => [$a['sort_order'], $a['id']] <=> [$b['sort_order'], $b['id']]);
        return $rows;
    }

    public function responseParamsForApi(int $apiId): array
    {
        $rows = [];
        foreach ($this->data['api_response_params'] ?? [] as $row) {
            if ((int)$row['api_id'] === $apiId) {
                $rows[] = $row;
            }
        }
        usort($rows, fn($a, $b) => [$a['sort_order'], $a['id']] <=> [$b['sort_order'], $b['id']]);
        return $rows;
    }

    public function saveApi(array $api, array $params, array $responseParams = [], int $id = 0): int
    {
        $this->assertUniqueRoute($api['route'], $id);
        $now = $this->now();

        if ($id > 0) {
            foreach ($this->data['apis'] as $idx => $row) {
                if ((int)$row['id'] === $id) {
                    $api['id'] = $id;
                    $api['call_count'] = (int)($row['call_count'] ?? 0);
                    $api['success_count'] = (int)($row['success_count'] ?? 0);
                    $api['avg_latency_ms'] = (int)($row['avg_latency_ms'] ?? 0);
                    $api['created_at'] = $row['created_at'] ?? $now;
                    $api['updated_at'] = $now;
                    $this->data['apis'][$idx] = $api;
                    $this->replaceParams($id, $params);
                    $this->replaceResponseParams($id, $responseParams);
                    $this->save();
                    return $id;
                }
            }
            throw new RuntimeException('API not found');
        }

        $id = $this->nextId('apis');
        $api['id'] = $id;
        $api['call_count'] = 0;
        $api['success_count'] = 0;
        $api['avg_latency_ms'] = 0;
        $api['created_at'] = $now;
        $api['updated_at'] = $now;
        $this->data['apis'][] = $api;
        $this->replaceParams($id, $params);
        $this->replaceResponseParams($id, $responseParams);
        $this->save();
        return $id;
    }

    private function assertUniqueRoute(string $route, int $ignoreId = 0): void
    {
        foreach ($this->data['apis'] as $api) {
            if ($api['route'] === $route && (int)$api['id'] !== $ignoreId) {
                throw new RuntimeException('路由已存在');
            }
        }
    }

    private function replaceParams(int $apiId, array $params): void
    {
        $this->data['api_params'] = array_values(array_filter(
            $this->data['api_params'],
            fn($row) => (int)$row['api_id'] !== $apiId
        ));
        foreach ($params as $index => $row) {
            $row['id'] = $this->nextId('api_params');
            $row['api_id'] = $apiId;
            $row['sort_order'] = $index;
            $this->data['api_params'][] = $row;
        }
    }

    private function replaceResponseParams(int $apiId, array $params): void
    {
        $this->data['api_response_params'] = array_values(array_filter(
            $this->data['api_response_params'] ?? [],
            fn($row) => (int)$row['api_id'] !== $apiId
        ));
        foreach ($params as $index => $row) {
            $row['id'] = $this->nextId('api_response_params');
            $row['api_id'] = $apiId;
            $row['sort_order'] = $index;
            $this->data['api_response_params'][] = $row;
        }
    }

    public function toggleApi(int $id): void
    {
        foreach ($this->data['apis'] as $idx => $api) {
            if ((int)$api['id'] === $id) {
                $this->data['apis'][$idx]['status'] = $api['status'] === 'published' ? 'disabled' : 'published';
                $this->data['apis'][$idx]['updated_at'] = $this->now();
                $this->save();
                return;
            }
        }
    }

    public function disableApi(int $id): void
    {
        foreach ($this->data['apis'] as $idx => $api) {
            if ((int)$api['id'] === $id) {
                $this->data['apis'][$idx]['status'] = 'disabled';
                $this->data['apis'][$idx]['updated_at'] = $this->now();
                $this->save();
                return;
            }
        }
    }

    public function deleteApi(int $id): void
    {
        $this->data['apis'] = array_values(array_filter($this->data['apis'], fn($api) => (int)$api['id'] !== $id));
        $this->data['api_params'] = array_values(array_filter($this->data['api_params'], fn($row) => (int)$row['api_id'] !== $id));
        $this->data['api_response_params'] = array_values(array_filter($this->data['api_response_params'] ?? [], fn($row) => (int)$row['api_id'] !== $id));
        $this->save();
    }


    public function allApiKeys(): array
    {
        $rows = $this->data['api_keys'] ?? [];
        usort($rows, fn($a, $b) => (int)($b['id'] ?? 0) <=> (int)($a['id'] ?? 0));
        return $rows;
    }

    public function pendingApiKeyCount(): int
    {
        $count = 0;
        foreach ($this->data['api_keys'] ?? [] as $row) {
            if (($row['status'] ?? '') === 'pending') {
                $count++;
            }
        }
        return $count;
    }

    public function createApiKey(string $email, string $purpose = '', string $ip = '', string $userAgent = ''): array
    {
        $now = $this->now();
        do {
            $uuid = $this->uuidV4();
        } while ($this->getApiKeyByUuid($uuid) !== null);

        $row = [
            'id' => $this->nextId('api_keys'),
            'uuid' => $uuid,
            'email' => $email,
            'purpose' => $purpose,
            'status' => 'pending',
            'ip' => $ip,
            'user_agent' => $userAgent,
            'created_at' => $now,
            'updated_at' => $now,
            'approved_at' => null,
        ];

        $this->data['api_keys'][] = $row;
        $this->save();
        return $row;
    }

    public function getApiKeyByUuid(string $uuid): ?array
    {
        $uuid = trim($uuid);
        if ($uuid === '') {
            return null;
        }

        foreach ($this->data['api_keys'] ?? [] as $row) {
            if (hash_equals((string)($row['uuid'] ?? ''), $uuid)) {
                return $row;
            }
        }
        return null;
    }

    public function activeApiKey(string $uuid): ?array
    {
        $row = $this->getApiKeyByUuid($uuid);
        if (!$row || ($row['status'] ?? '') !== 'active') {
            return null;
        }
        return $row;
    }

    public function setApiKeyStatus(int $id, string $status): void
    {
        if (!in_array($status, ['pending', 'active', 'disabled'], true)) {
            throw new RuntimeException('Invalid API Key status');
        }

        foreach ($this->data['api_keys'] ?? [] as $idx => $row) {
            if ((int)($row['id'] ?? 0) === $id) {
                $now = $this->now();
                $this->data['api_keys'][$idx]['status'] = $status;
                $this->data['api_keys'][$idx]['updated_at'] = $now;
                if ($status === 'active' && empty($this->data['api_keys'][$idx]['approved_at'])) {
                    $this->data['api_keys'][$idx]['approved_at'] = $now;
                }
                $this->save();
                return;
            }
        }
    }

    public function logCall(array $api, int $statusCode, int $latencyMs, bool $success = false): void
    {
        $this->data['api_call_logs'][] = [
            'id' => $this->nextId('api_call_logs'),
            'api_id' => (int)$api['id'],
            'route' => $api['route'],
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
            'method' => $_SERVER['REQUEST_METHOD'] ?? 'GET',
            'status_code' => $statusCode,
            'latency_ms' => $latencyMs,
            'created_at' => $this->now(),
        ];
        foreach ($this->data['apis'] as $idx => $row) {
            if ((int)$row['id'] === (int)$api['id']) {
                $this->data['apis'][$idx]['call_count'] = (int)$row['call_count'] + 1;
                $this->data['apis'][$idx]['avg_latency_ms'] = (int)round((((int)$row['avg_latency_ms']) + $latencyMs) / 2);
                if ($success) {
                    $this->data['apis'][$idx]['success_count'] = (int)$row['success_count'] + 1;
                }
                break;
            }
        }
        $this->save();
    }
}

function db(): FileDatabase
{
    static $db = null;
    if ($db instanceof FileDatabase) {
        return $db;
    }
    $db = new FileDatabase(__DIR__ . '/../database/data.json');
    return $db;
}

function ensure_database(FileDatabase $db, bool $force = false): void
{
    if ($force) {
        $db->reset();
    }
}

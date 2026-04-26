<!doctype html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= h($title) ?> - 小锋学长的API Hub</title>
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body>
<header class="topbar">
    <a class="brand" href="/">
        <span class="brand-logo">API</span>
        <strong>小锋学长的API Hub</strong>
    </a>
    <nav><a href="/" class="nav-link">接口列表</a><a href="/admin/apis" class="nav-link">后台管理</a></nav>
</header>

<?php
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$apiUrl = $scheme . '://' . $host . '/api/' . $api['route'];
$examplePairs = [];
foreach ($params as $p) {
    $name = (string)($p['param_name'] ?? '');
    if ($name === '') {
        continue;
    }
    $value = (string)($p['example_value'] ?? '');
    if ($value === '') {
        $value = match ($p['param_type'] ?? 'string') {
            'int', 'integer', 'number' => '1',
            'bool', 'boolean' => 'true',
            default => 'demo',
        };
    }
    $examplePairs[$name] = $value;
}
$queryString = $examplePairs ? '?' . http_build_query($examplePairs) : '';
$exampleUrl = $apiUrl . $queryString;
$returnExample = [
    'code' => 0,
    'msg' => 'success',
    'data' => [
        'route' => $api['route'],
        'example' => true,
    ],
];
if (empty($responseParams)) {
    $responseParams = [
        ['param_name'=>'code','param_type'=>'integer','description'=>'业务状态码，0 表示请求成功。','example_value'=>'0'],
        ['param_name'=>'msg','param_type'=>'string','description'=>'接口调用结果说明。','example_value'=>'success'],
        ['param_name'=>'data','param_type'=>'object','description'=>'接口返回的业务数据。','example_value'=>'{}'],
    ];
}
?>

<section class="doc-clean-hero">
    <div class="doc-clean-inner">
        <div class="doc-clean-main">
            <div class="doc-breadcrumb"><a href="/">接口列表</a><span>/</span><span>接口文档</span></div>
            <div class="doc-clean-title-row">
                <?= icon_svg($api['icon'], $api['name']) ?>
                <div class="doc-title-text">
                    <div class="doc-label-row">
                        <span><?= h($api['category_name'] ?: '未分类') ?></span>
                        <span><?= h(badge_access($api['access_level'])) ?></span>
                        <span>JSON</span>
                    </div>
                    <h1><?= h($api['name']) ?></h1>
                    <p><?= h($api['description'] ?: '暂无接口简介。') ?></p>
                </div>
            </div>
            <div class="endpoint-strip">
                <span>接口地址</span>
                <code><?= h($apiUrl) ?></code>
            </div>
        </div>
        <aside class="doc-clean-summary" aria-label="接口概览">
            <div><span>请求方式</span><strong><?= h($api['method_set']) ?></strong></div>
            <div><span>累计调用</span><strong><?= h(format_count((int)$api['call_count'])) ?></strong></div>
            <div><span>今日调用</span><strong><?= h(format_count((int)($api['today_call_count'] ?? 0))) ?></strong></div>
            <div><span>平均延迟</span><strong><?= h((string)$api['avg_latency_ms']) ?>ms</strong></div>
        </aside>
    </div>
</section>

<main class="doc-clean-page doc-clean-page-full">
    <div class="doc-clean-content">
        <section id="overview" class="doc-clean-card">
            <div class="doc-section-title">
                <span>01</span>
                <div><h2>基本信息</h2><p>接口调用前需要确认的核心配置。</p></div>
            </div>
            <div class="overview-grid">
                <div class="overview-item"><span>接口地址</span><code><?= h('/api/' . $api['route']) ?></code></div>
                <div class="overview-item"><span>请求方式</span><strong><?= h($api['method_set']) ?></strong></div>
                <div class="overview-item"><span>返回格式</span><strong><?= h($api['response_format']) ?></strong></div>
                <div class="overview-item"><span>调用权限</span><strong><?= h(badge_access($api['access_level'])) ?></strong></div>
                <div class="overview-item"><span>创建时间</span><strong><?= h(substr((string)$api['created_at'], 0, 10)) ?></strong></div>
                <div class="overview-item"><span>脚本路由</span><code><?= h($api['route']) ?></code></div>
            </div>
        </section>

        <section id="params" class="doc-clean-card">
            <div class="doc-section-title">
                <span>02</span>
                <div><h2>请求参数说明</h2><p>必填参数必须传入，示例值可直接用于联调。</p></div>
            </div>
            <div class="doc-table-wrap clean-table-wrap">
                <table class="data-table doc-data-table clean-doc-table">
                    <colgroup>
                        <col style="width:18%"><col style="width:12%"><col style="width:16%"><col style="width:34%"><col style="width:20%">
                    </colgroup>
                    <thead>
                    <tr>
                        <th>参数名</th>
                        <th>必填</th>
                        <th>类型</th>
                        <th>说明</th>
                        <th>示例值</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($params as $p): ?>
                        <tr>
                            <td><code><?= h($p['param_name']) ?></code></td>
                            <td><?= $p['required'] ? '<span class="badge orange">是</span>' : '<span class="badge muted">否</span>' ?></td>
                            <td><?= h($p['param_type']) ?></td>
                            <td><?= h($p['description']) ?></td>
                            <td><code><?= h($p['example_value']) ?></code></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$params): ?>
                        <tr><td colspan="5">无请求参数。</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <section id="examples" class="doc-clean-card">
            <div class="doc-section-title">
                <span>03</span>
                <div><h2>示例代码</h2><p>优先使用示例地址测试请求参数是否正确。</p></div>
            </div>
            <div class="example-url-box">
                <span>示例请求</span>
                <code><?= h($exampleUrl) ?></code>
            </div>
            <div class="code-clean-grid">
                <article class="code-card">
                    <h3>cURL</h3>
                    <pre><code>curl "<?= h($exampleUrl) ?>"</code></pre>
                </article>
                <article class="code-card">
                    <h3>PHP</h3>
                    <pre><code>$url = '<?= h($exampleUrl) ?>';
$response = file_get_contents($url);
$data = json_decode($response, true);</code></pre>
                </article>
                <article class="code-card">
                    <h3>JavaScript</h3>
                    <pre><code>fetch('<?= h($exampleUrl) ?>')
  .then(res =&gt; res.json())
  .then(data =&gt; console.log(data));</code></pre>
                </article>
            </div>
        </section>

        <section id="response" class="doc-clean-card">
            <div class="doc-section-title">
                <span>04</span>
                <div><h2>返回说明</h2><p>实际返回内容以接口脚本处理结果为准。</p></div>
            </div>
            <div class="response-layout">
                <div>
                    <h3 class="subsection-title">返回示例</h3>
                    <pre class="return-example"><code><?= h(json_encode($returnExample, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)) ?></code></pre>
                </div>
                <div>
                    <h3 class="subsection-title">返回参数说明</h3>
                    <div class="doc-table-wrap clean-table-wrap">
                        <table class="data-table doc-data-table clean-doc-table return-param-table">
                            <colgroup>
                                <col style="width:22%"><col style="width:18%"><col style="width:40%"><col style="width:20%">
                            </colgroup>
                            <thead>
                            <tr>
                                <th>参数名</th>
                                <th>类型</th>
                                <th>说明</th>
                                <th>示例值</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($responseParams as $p): ?>
                                <tr>
                                    <td><code><?= h($p['param_name']) ?></code></td>
                                    <td><?= h($p['param_type']) ?></td>
                                    <td><?= h($p['description']) ?></td>
                                    <td><code><?= h($p['example_value']) ?></code></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
</main>

<?php include __DIR__ . '/../partials/footer.php'; ?>
</body>
</html>

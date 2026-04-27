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
$allowedMethods = array_values(array_filter(array_map(static fn($m) => strtoupper(trim((string)$m)), explode(',', (string)$api['method_set']))));
if (!$allowedMethods) {
    $allowedMethods = ['GET'];
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

<main class="doc-clean-page doc-clean-page-full doc-tabs-page">
    <section class="doc-tab-shell" aria-label="接口文档详情">
        <div class="doc-tab-nav" role="tablist" aria-label="接口文档导航">
            <button class="doc-tab-button is-active" id="tab-overview" type="button" role="tab" aria-selected="true" aria-controls="panel-overview" data-doc-tab="overview"><span>ⓘ</span>接口详情</button>
            <button class="doc-tab-button" id="tab-params" type="button" role="tab" aria-selected="false" aria-controls="panel-params" data-doc-tab="params"><span>☷</span>请求参数</button>
            <button class="doc-tab-button" id="tab-examples" type="button" role="tab" aria-selected="false" aria-controls="panel-examples" data-doc-tab="examples"><span>&lt;/&gt;</span>示例代码</button>
            <button class="doc-tab-button" id="tab-response" type="button" role="tab" aria-selected="false" aria-controls="panel-response" data-doc-tab="response"><span>↩</span>返回示例</button>
            <button class="doc-tab-button debug-tab" id="tab-debug" type="button" role="tab" aria-selected="false" aria-controls="panel-debug" data-doc-tab="debug"><span>🐞</span>在线调试</button>
        </div>

        <div class="doc-tab-panels">
            <section id="panel-overview" class="doc-tab-panel is-active" role="tabpanel" aria-labelledby="tab-overview" data-doc-panel="overview">
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

            <section id="panel-params" class="doc-tab-panel" role="tabpanel" aria-labelledby="tab-params" data-doc-panel="params" hidden>
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

            <section id="panel-examples" class="doc-tab-panel" role="tabpanel" aria-labelledby="tab-examples" data-doc-panel="examples" hidden>
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

            <section id="panel-response" class="doc-tab-panel" role="tabpanel" aria-labelledby="tab-response" data-doc-panel="response" hidden>
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

            <section id="panel-debug" class="doc-tab-panel" role="tabpanel" aria-labelledby="tab-debug" data-doc-panel="debug" hidden>
                <div class="debug-layout">
                    <form class="debug-form-card" id="api-debug-form">
                        <div class="debug-card-title">调试参数</div>
                        <label class="debug-field">请求方式
                            <select id="debug-method" name="_method">
                                <?php foreach ($allowedMethods as $method): ?>
                                    <option value="<?= h($method) ?>"><?= h($method) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                        <label class="debug-field">接口地址
                            <input id="debug-endpoint" value="<?= h($apiUrl) ?>" readonly>
                        </label>
                        <?php foreach ($params as $p): ?>
                            <?php
                            $debugName = (string)($p['param_name'] ?? '');
                            if ($debugName === '') {
                                continue;
                            }
                            $debugExample = (string)($p['example_value'] ?? '');
                            ?>
                            <label class="debug-field"><?= h($debugName) ?><?= !empty($p['required']) ? ' <em>*</em>' : '' ?>
                                <input class="debug-param-input" data-param-name="<?= h($debugName) ?>" value="<?= h($debugExample) ?>" placeholder="<?= h($p['description'] ?: '请输入参数值') ?>">
                            </label>
                        <?php endforeach; ?>
                        <?php if (!$params): ?>
                            <div class="debug-empty">该接口未配置请求参数，可直接发送请求。</div>
                        <?php endif; ?>
                        <button class="primary-btn debug-submit" type="submit">➤ 发送请求</button>
                    </form>

                    <div class="debug-response-card">
                        <div class="debug-response-head">
                            <strong>响应结果</strong>
                            <div class="debug-response-actions">
                                <span id="debug-elapsed">◔ --</span>
                                <button class="debug-copy-btn" id="debug-copy" type="button">📋 复制</button>
                            </div>
                        </div>
                        <pre class="debug-response-body"><code id="debug-result">点击左侧“发送请求”查看结果...</code></pre>
                    </div>
                </div>
            </section>
        </div>
    </section>
</main>

<script>
(function () {
    const tabs = Array.from(document.querySelectorAll('[data-doc-tab]'));
    const panels = Array.from(document.querySelectorAll('[data-doc-panel]'));

    function activateTab(name, updateHash) {
        tabs.forEach(tab => {
            const active = tab.dataset.docTab === name;
            tab.classList.toggle('is-active', active);
            tab.setAttribute('aria-selected', active ? 'true' : 'false');
        });
        panels.forEach(panel => {
            const active = panel.dataset.docPanel === name;
            panel.classList.toggle('is-active', active);
            panel.hidden = !active;
        });
        if (updateHash) {
            history.replaceState(null, '', '#' + name);
        }
    }

    tabs.forEach(tab => {
        tab.addEventListener('click', () => activateTab(tab.dataset.docTab, true));
    });

    const initialTab = location.hash.replace('#', '');
    if (tabs.some(tab => tab.dataset.docTab === initialTab)) {
        activateTab(initialTab, false);
    }

    const form = document.getElementById('api-debug-form');
    const methodSelect = document.getElementById('debug-method');
    const endpointInput = document.getElementById('debug-endpoint');
    const resultEl = document.getElementById('debug-result');
    const elapsedEl = document.getElementById('debug-elapsed');
    const copyButton = document.getElementById('debug-copy');

    function formatResponse(text) {
        try {
            return JSON.stringify(JSON.parse(text), null, 2);
        } catch (e) {
            return text || '(empty response)';
        }
    }

    if (form) {
        form.addEventListener('submit', async event => {
            event.preventDefault();
            const method = (methodSelect.value || 'GET').toUpperCase();
            const endpoint = endpointInput.value;
            const params = new URLSearchParams();
            const body = {};

            document.querySelectorAll('.debug-param-input').forEach(input => {
                const name = input.dataset.paramName;
                const value = input.value;
                if (!name || value === '') {
                    return;
                }
                params.append(name, value);
                body[name] = value;
            });

            let requestUrl = endpoint;
            const options = { method: method, headers: { 'Accept': 'application/json' } };
            if (method === 'GET') {
                const query = params.toString();
                if (query) {
                    requestUrl += (endpoint.includes('?') ? '&' : '?') + query;
                }
            } else {
                options.headers['Content-Type'] = 'application/json';
                options.body = JSON.stringify(body);
            }

            resultEl.textContent = '请求中...';
            elapsedEl.textContent = '◔ --';
            const started = performance.now();

            try {
                const response = await fetch(requestUrl, options);
                const text = await response.text();
                const elapsed = Math.round(performance.now() - started);
                elapsedEl.textContent = '◔ ' + elapsed + 'ms';
                resultEl.textContent = formatResponse(text);
            } catch (error) {
                const elapsed = Math.round(performance.now() - started);
                elapsedEl.textContent = '◔ ' + elapsed + 'ms';
                resultEl.textContent = '请求失败：' + error.message;
            }
        });
    }

    if (copyButton) {
        copyButton.addEventListener('click', async () => {
            const text = resultEl.textContent || '';
            try {
                await navigator.clipboard.writeText(text);
                const oldText = copyButton.textContent;
                copyButton.textContent = '已复制';
                setTimeout(() => { copyButton.textContent = oldText; }, 1200);
            } catch (e) {
                alert('复制失败，请手动复制响应内容');
            }
        });
    }
})();
</script>

<?php include __DIR__ . '/../partials/footer.php'; ?>
</body>
</html>

<!doctype html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= h($title) ?> - 小锋学长的API Hub</title>
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body>
<header class="topbar admin-topbar">
    <a class="brand" href="/admin/apis"><span class="brand-logo">API</span><strong>后台管理</strong></a>
    <nav>
        <a class="nav-link" href="/">前台</a>
        <a class="nav-link" href="/admin/logout">退出</a>
    </nav>
</header>
<main class="admin-page">
    <div class="admin-head">
        <div>
            <h1>API 管理</h1>
            <p>新增、编辑、发布后会自动同步到前台接口列表。</p>
        </div>
        <a class="primary-btn" href="/admin/apis/create">+ 新增API</a>
    </div>

    <div class="table-card admin-table-card">
        <div class="table-toolbar">
            <div class="table-title">
                <strong>接口清单</strong>
                <small>共 <?= h((string)count($apis)) ?> 个 API，按 ID 升序排列</small>
            </div>
            <span class="table-chip">ID ↑ 升序</span>
        </div>
        <div class="table-scroll">
            <table class="data-table admin-table api-list-table">
                <colgroup>
                    <col class="admin-col-id">
                    <col class="admin-col-name">
                    <col class="admin-col-route">
                    <col class="admin-col-script">
                    <col class="admin-col-category">
                    <col class="admin-col-status">
                    <col class="admin-col-calls">
                    <col class="admin-col-actions">
                </colgroup>
                <thead>
                <tr>
                    <th class="col-id">ID</th>
                    <th class="col-name">名称</th>
                    <th class="col-route">路由</th>
                    <th class="col-script">脚本</th>
                    <th class="col-category">分类</th>
                    <th class="col-status">状态</th>
                    <th class="col-calls">调用</th>
                    <th class="col-actions">操作</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($apis as $api): ?>
                    <?php
                    $statusClass = $api['status'] === 'published' ? 'published' : ($api['status'] === 'disabled' ? 'disabled' : 'draft');
                    $statusText = $api['status'] === 'published' ? '已发布' : ($api['status'] === 'disabled' ? '已禁用' : '草稿');
                    ?>
                    <tr>
                        <td><span class="id-pill">#<?= h((string)$api['id']) ?></span></td>
                        <td>
                            <div class="api-name-cell">
                                <?= icon_svg($api['icon'], $api['name']) ?>
                                <div class="api-name-meta">
                                    <a class="api-name-link" href="/api-doc/<?= h($api['route']) ?>" target="_blank" title="查看接口详情"><?= h($api['name']) ?></a>
                                    <small><?= h($api['description'] ?: '暂无接口说明') ?></small>
                                </div>
                            </div>
                        </td>
                        <td><code class="route-code" title="/api/<?= h($api['route']) ?>">/api/<?= h($api['route']) ?></code></td>
                        <td><code class="script-code" title="<?= h($api['script_file']) ?>"><?= h($api['script_file']) ?></code></td>
                        <td><?= h($api['category_name'] ?? '-') ?></td>
                        <td><span class="status-pill <?= h($statusClass) ?>"><?= h($statusText) ?></span></td>
                        <td class="col-calls"><span class="call-pill" title="<?= h(number_format((int)$api['call_count'])) ?>"><?= h(format_count((int)$api['call_count'])) ?></span></td>
                        <td class="actions">
                            <a class="primary-action" href="/admin/apis/edit?id=<?= h((string)$api['id']) ?>">编辑</a>
                            <?php if ($api['status'] === 'disabled'): ?>
                                <span class="disabled-action" title="该接口已禁用">禁用</span>
                            <?php else: ?>
                                <a href="/admin/apis/disable?id=<?= h((string)$api['id']) ?>">禁用</a>
                            <?php endif; ?>
                            <a class="danger" onclick="return confirm('确定删除这个API吗？')" href="/admin/apis/delete?id=<?= h((string)$api['id']) ?>">删除</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>
<?php include __DIR__ . '/../partials/footer.php'; ?>
</body>
</html>

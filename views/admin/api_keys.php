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
        <a class="nav-link" href="/admin/apis">API列表</a>
        <a class="nav-link" href="/admin/api-keys">Key审核</a>
        <a class="nav-link" href="/">前台</a>
        <a class="nav-link" href="/admin/logout">退出</a>
    </nav>
</header>
<?php
$keyStats = [
    'total' => count($keys),
    'pending' => 0,
    'active' => 0,
    'disabled' => 0,
];
foreach ($keys as $item) {
    $status = (string)($item['status'] ?? 'pending');
    if (isset($keyStats[$status])) {
        $keyStats[$status]++;
    }
}
$statusLabels = [
    'pending' => '待审核',
    'active' => '有效',
    'disabled' => '无效',
];
$statusClasses = [
    'pending' => 'draft',
    'active' => 'published',
    'disabled' => 'disabled',
];
?>
<main class="admin-page key-admin-page">
    <div class="admin-head key-admin-head">
        <div>
            <h1>ApiKey 审核</h1>
            <p>查看前台用户申请的 UUID Key，根据邮箱和用途判断是否通过。通过后的 Key 可以调用需要 Key 的接口。</p>
        </div>
        <a class="secondary-btn" href="/admin/apis">返回API列表</a>
    </div>

    <section class="key-stats-grid">
        <div class="key-stat-card">
            <span>全部 Key</span>
            <strong><?= h((string)$keyStats['total']) ?></strong>
        </div>
        <div class="key-stat-card pending">
            <span>待审核</span>
            <strong><?= h((string)$keyStats['pending']) ?></strong>
        </div>
        <div class="key-stat-card active">
            <span>有效</span>
            <strong><?= h((string)$keyStats['active']) ?></strong>
        </div>
        <div class="key-stat-card disabled">
            <span>无效</span>
            <strong><?= h((string)$keyStats['disabled']) ?></strong>
        </div>
    </section>

    <div class="table-card admin-table-card key-list-card">
        <div class="table-toolbar key-table-toolbar">
            <div class="table-title">
                <strong>Key 清单</strong>
                <small>列表按申请时间倒序展示，邮箱为申请人提供的联系信息。</small>
            </div>
            <span class="table-chip">待审核 <?= h((string)$pendingKeyCount) ?></span>
        </div>
        <div class="table-scroll">
            <table class="data-table admin-table key-list-table">
                <thead>
                <tr>
                    <th>状态</th>
                    <th>申请信息</th>
                    <th>UUID</th>
                    <th>申请来源</th>
                    <th>时间</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($keys as $key): ?>
                    <?php
                    $status = (string)($key['status'] ?? 'pending');
                    $statusText = $statusLabels[$status] ?? '待审核';
                    $statusClass = $statusClasses[$status] ?? 'draft';
                    $email = trim((string)($key['email'] ?? ''));
                    $purpose = trim((string)($key['purpose'] ?? ''));
                    ?>
                    <tr>
                        <td>
                            <div class="key-status-cell">
                                <small>#<?= h((string)$key['id']) ?></small>
                                <span class="status-pill <?= h($statusClass) ?>"><?= h($statusText) ?></span>
                            </div>
                        </td>
                        <td>
                            <div class="key-applicant-cell">
                                <strong><?= h($email !== '' ? $email : '未填写邮箱') ?></strong>
                                <?php if ($purpose !== ''): ?>
                                    <p><?= nl2br(h($purpose)) ?></p>
                                <?php else: ?>
                                    <p class="muted">未填写用途</p>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td>
                            <code class="key-code"><?= h($key['uuid']) ?></code>
                        </td>
                        <td>
                            <div class="key-source-cell">
                                <span><?= h($key['ip'] ?? '-') ?></span>
                                <?php $userAgent = (string)($key['user_agent'] ?? ''); ?>
                                <small title="<?= h($userAgent) ?>"><?= h($userAgent !== '' ? (strlen($userAgent) > 42 ? substr($userAgent, 0, 42) . '...' : $userAgent) : '-') ?></small>
                            </div>
                        </td>
                        <td>
                            <div class="key-time-cell">
                                <span>申请 <?= h($key['created_at'] ?? '-') ?></span>
                                <small>审核 <?= h($key['approved_at'] ?? '-') ?></small>
                            </div>
                        </td>
                        <td class="actions key-actions-cell">
                            <div class="key-actions">
                                <?php if ($status !== 'active'): ?>
                                    <form method="post" action="/admin/api-keys/status">
                                        <input type="hidden" name="_token" value="<?= h(csrf_token()) ?>">
                                        <input type="hidden" name="id" value="<?= h((string)$key['id']) ?>">
                                        <input type="hidden" name="status" value="active">
                                        <button class="primary-action" type="submit">通过</button>
                                    </form>
                                <?php endif; ?>
                                <?php if ($status !== 'disabled'): ?>
                                    <form method="post" action="/admin/api-keys/status">
                                        <input type="hidden" name="_token" value="<?= h(csrf_token()) ?>">
                                        <input type="hidden" name="id" value="<?= h((string)$key['id']) ?>">
                                        <input type="hidden" name="status" value="disabled">
                                        <button class="danger" type="submit" onclick="return confirm('确定将该 Key 设置为无效吗？')">设为无效</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$keys): ?>
                    <tr>
                        <td colspan="6">
                            <div class="empty-key-state">
                                <strong>暂无 Key 申请</strong>
                                <p>前台用户提交申请后，会出现在这里等待审核。</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>
<?php include __DIR__ . '/../partials/footer.php'; ?>
</body>
</html>

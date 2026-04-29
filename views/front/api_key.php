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
    <nav>
        <a href="/" class="nav-link">接口列表</a>
        <a href="/admin/apis" class="nav-link">后台管理</a>
    </nav>
</header>
<?php
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
$statusTips = [
    'pending' => '申请已提交，正在等待管理员审核。',
    'active' => '审核已通过，可以调用需要 Key 的接口。',
    'disabled' => '该 Key 当前为无效状态，不能调用需要 Key 的接口。',
];
?>
<main class="page home-page api-key-page">
    <section class="list-head api-key-hero">
        <div class="hero-copy">
            <span class="hero-eyebrow">ApiKey · 审核后生效</span>
            <h1><span class="cube">◆</span> 申请 ApiKey</h1>
            <p>填写邮箱并提交申请后，系统会生成一个 UUID Key。该 Key 初始为待审核状态，需要管理员审核通过后才能调用需要 Key 的接口。</p>
        </div>
        <div class="api-key-side-card">
            <strong>申请流程</strong>
            <ol>
                <li>填写邮箱和用途说明。</li>
                <li>提交后复制并保存 UUID Key。</li>
                <li>管理员审核通过后，将 <code>key</code> 作为请求参数传入。</li>
            </ol>
        </div>
    </section>

    <section class="table-card admin-table-card api-key-apply-card">
        <div class="table-toolbar">
            <div class="table-title">
                <strong>申请信息</strong>
                <small>邮箱为必填项，用途可选。当前没有登录体系，请自行保存生成的 UUID。</small>
            </div>
        </div>
        <div class="api-key-apply-body">
            <?php if (!empty($apiKeyError)): ?>
                <div class="alert error"><?= h($apiKeyError) ?></div>
            <?php endif; ?>

            <?php if (is_array($createdKey ?? null)): ?>
                <?php
                $createdStatus = (string)($createdKey['status'] ?? 'pending');
                $createdStatusText = $statusLabels[$createdStatus] ?? '待审核';
                $createdStatusClass = $statusClasses[$createdStatus] ?? 'draft';
                $createdTip = $statusTips[$createdStatus] ?? '申请状态未知，请联系管理员确认。';
                $createdAlertClass = $createdStatus === 'active' ? 'success' : ($createdStatus === 'disabled' ? 'error' : 'warning');
                ?>
                <div class="alert <?= h($createdAlertClass) ?> api-key-created-alert">
                    <span>当前状态</span>
                    <strong class="status-pill <?= h($createdStatusClass) ?>"><?= h($createdStatusText) ?></strong>
                    <p><?= h($createdTip) ?></p>
                </div>
                <div class="api-key-result">
                    <div>
                        <span>你的 ApiKey</span>
                        <code><?= h($createdKey['uuid']) ?></code>
                    </div>
                    <div>
                        <span>申请邮箱</span>
                        <strong><?= h($createdKey['email'] ?? '') ?></strong>
                    </div>
                    <?php if (!empty($createdKey['purpose'])): ?>
                        <div>
                            <span>用途</span>
                            <p><?= nl2br(h($createdKey['purpose'])) ?></p>
                        </div>
                    <?php endif; ?>
                </div>
                <p class="api-key-help">调用需要 Key 的接口时，在请求参数中增加 <code>key=<?= h($createdKey['uuid']) ?></code>。</p>
            <?php endif; ?>

            <form class="api-key-form" method="post" action="/api-key/apply">
                <input type="hidden" name="_token" value="<?= h(csrf_token()) ?>">
                <label>
                    <span>邮箱 <em>*</em></span>
                    <input type="email" name="email" value="<?= h((string)($apiKeyOld['email'] ?? '')) ?>" placeholder="例如 name@example.com" required>
                </label>
                <label>
                    <span>用途说明</span>
                    <textarea name="purpose" rows="5" placeholder="可简单描述你准备用该 Key 调用哪些 API、用于什么场景。"><?= h((string)($apiKeyOld['purpose'] ?? '')) ?></textarea>
                </label>
                <button class="primary-btn" type="submit">生成并提交审核</button>
            </form>
        </div>
    </section>

</main>
<?php include __DIR__ . '/../partials/footer.php'; ?>
</body>
</html>

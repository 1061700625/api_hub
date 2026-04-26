<?php
$totalApis = count($apis);
$totalCalls = array_sum(array_map(static fn($item) => (int)($item['call_count'] ?? 0), $apis));
$totalTodayCalls = array_sum(array_map(static fn($item) => (int)($item['today_call_count'] ?? 0), $apis));
?>
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
        <a href="/admin/apis" class="nav-link">后台管理</a>
    </nav>
</header>

<main class="page home-page">
    <section class="list-head">
        <div class="hero-copy">
            <span class="hero-eyebrow">稳定 · 免费 · JSON 接口</span>
            <h1><span class="cube">◆</span> API 接口列表</h1>
            <p>集中展示所有已发布接口，后台新增或发布后会自动同步到这里。你可以按名称、说明、路由和分类快速筛选。</p>
            <div class="hero-stats">
                <div class="stat-card" title="已发布接口：<?= h((string)$totalApis) ?> 个"><span class="stat-num"><?= h((string)$totalApis) ?></span><span class="stat-label">接口总数</span></div>
                <div class="stat-card" title="所有接口累计调用：<?= h(number_format((int)$totalCalls)) ?> 次"><span class="stat-num"><?= h(format_count((int)$totalCalls)) ?></span><span class="stat-label">累计调用</span></div>
                <div class="stat-card" title="所有接口今日调用：<?= h(number_format((int)$totalTodayCalls)) ?> 次"><span class="stat-num"><?= h(format_count((int)$totalTodayCalls)) ?></span><span class="stat-label">今日调用</span></div>
            </div>
        </div>
        <form class="filters" method="get" action="/">
            <input type="search" name="keyword" placeholder="搜索接口名称、说明或路由" value="<?= h($_GET['keyword'] ?? '') ?>">
            <select name="category">
                <option value="">全部分类</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= h($cat['id']) ?>" <?= (string)($_GET['category'] ?? '') === (string)$cat['id'] ? 'selected' : '' ?>><?= h($cat['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit">立即筛选</button>
        </form>
    </section>

    <section class="api-grid">
        <?php foreach ($apis as $item): ?>
            <a class="api-card" href="/api-doc/<?= h($item['route']) ?>">
                <div>
                    <div class="card-main">
                        <?= icon_svg($item['icon'], $item['name']) ?>
                        <div>
                            <h2><?= h($item['name']) ?></h2>
                            <div class="badges">
                                <span class="badge green">● 正常</span>
                                <span class="badge light">🎁 <?= h(badge_access($item['access_level'])) ?></span>
                                <?php if (!empty($item['category_name'])): ?><span class="badge muted"><?= h($item['category_name']) ?></span><?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <p class="card-desc"><?= h(excerpt($item['description'] ?? '', 92)) ?></p>
                </div>
                <div class="card-meta card-metrics">
                    <span title="累计调用：<?= h(number_format((int)$item['call_count'])) ?>"><em>累计调用</em><strong><?= h(format_count((int)$item['call_count'])) ?></strong></span>
                    <span title="今日调用：<?= h(number_format((int)($item['today_call_count'] ?? 0))) ?>"><em>今日调用</em><strong><?= h(format_count((int)($item['today_call_count'] ?? 0))) ?></strong></span>
                    <span class="created-metric" title="创建时间：<?= h($item['created_at']) ?>"><em>创建时间</em><strong><?= h(substr($item['created_at'], 0, 10)) ?></strong></span>
                </div>
            </a>
        <?php endforeach; ?>
    </section>

    <?php if (!$apis): ?>
        <div class="empty">暂无接口。</div>
    <?php endif; ?>
</main>
<?php include __DIR__ . '/../partials/footer.php'; ?>
</body>
</html>

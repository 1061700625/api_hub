<!doctype html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= h($title) ?> - 小锋学长的API Hub</title>
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body class="admin-bg">
<div class="login-card">
    <div class="brand login-brand"><span class="brand-logo">API</span><strong>小锋学长的API Hub 后台</strong></div>
    <p class="hint">默认账号：admin，默认密码：admin123。上线前请修改。</p>
    <?php if ($error): ?><div class="alert error"><?= h($error) ?></div><?php endif; ?>
    <form method="post" autocomplete="off">
        <input type="hidden" name="_token" value="<?= h(csrf_token()) ?>">
        <label>用户名</label>
        <input name="username" value="admin" autocomplete="username" required>
        <label>密码</label>
        <input name="password" type="password" autocomplete="off" required>
        <button class="primary-btn" type="submit">登录后台</button>
    </form>
    <a class="back-link" href="/">返回前台</a>
</div>
<?php include __DIR__ . '/../partials/footer.php'; ?>
</body>
</html>

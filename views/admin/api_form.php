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
    <nav><a class="nav-link" href="/admin/apis">API列表</a><a class="nav-link" href="/admin/api-keys">Key审核</a><a class="nav-link" href="/">前台</a></nav>
</header>
<main class="admin-page form-page">
    <div class="admin-head form-head">
        <div>
            <span class="page-kicker"><?= $api['id'] ? '编辑接口' : '创建接口' ?></span>
            <h1><?= h($title) ?></h1>
            <p>填写接口基础信息、上传 PHP 脚本，并完善请求和返回参数文档。</p>
        </div>
        <a class="secondary-btn" href="/admin/apis">返回列表</a>
    </div>

    <?php if ($error): ?><div class="alert error"><?= h($error) ?></div><?php endif; ?>

    <form class="form-card api-edit-form" method="post" enctype="multipart/form-data">
        <input type="hidden" name="_token" value="<?= h(csrf_token()) ?>">

        <section class="form-section">
            <div class="form-section-title">
                <span>01</span>
                <div>
                    <h2>基础信息</h2>
                    <p>这些信息会展示在前台列表和接口详情页。</p>
                </div>
            </div>
            <div class="form-grid form-grid-2">
                <label>API名称<input name="name" value="<?= h($api['name']) ?>" placeholder="例如 IP归属地查询" required></label>
                <label>路由<input name="route" value="<?= h($api['route']) ?>" placeholder="例如 ip_query" required></label>
                <label class="span-2">接口说明<textarea name="description" rows="4" placeholder="简要说明接口能力、适用场景和注意事项"><?= h($api['description']) ?></textarea></label>
            </div>
        </section>

        <section class="form-section">
            <div class="form-section-title">
                <span>02</span>
                <div>
                    <h2>脚本与分类</h2>
                    <p>脚本上传后会保存到项目 api/ 目录，文件名默认使用当前路由。</p>
                </div>
            </div>
            <div class="form-grid form-grid-2 align-start">
                <label class="file-field span-2">脚本文件
                    <div class="upload-box">
                        <div>
                            <strong>上传 PHP 脚本</strong>
                            <p><?= !empty($api['script_file']) ? '当前脚本：' . h($api['script_file']) . '，重新上传会替换绑定脚本。' : '请选择一个 .php 文件，保存时会放入 api/ 目录。' ?></p>
                        </div>
                        <input type="file" name="script_upload" accept=".php" <?= empty($api['script_file']) ? 'required' : '' ?>>
                    </div>
                </label>

                <label>分类
                    <select name="category_id">
                        <option value="">无分类</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= h((string)$cat['id']) ?>" <?= (string)$api['category_id'] === (string)$cat['id'] ? 'selected' : '' ?>><?= h($cat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label>新增分类<input name="new_category_name" placeholder="填写后优先使用新增分类"></label>
            </div>
        </section>

        <section class="form-section">
            <div class="form-section-title">
                <span>03</span>
                <div>
                    <h2>展示与权限</h2>
                    <p>控制接口展示图标、调用权限、状态和支持的请求方式。</p>
                </div>
            </div>
            <div class="form-grid form-grid-3">
                <label>图标标识<input name="icon" value="<?= h($api['icon']) ?>" placeholder="可为空，也可填写图片链接或路径"></label>
                <label>调用权限
                    <select name="access_level">
                        <option value="免费" <?= $api['access_level'] === '免费' || $api['access_level'] === '公开/免费' ? 'selected' : '' ?>>免费</option>
                        <option value="付费" <?= $api['access_level'] === '付费' ? 'selected' : '' ?>>付费</option>
                        <option value="登录" <?= $api['access_level'] === '登录' ? 'selected' : '' ?>>登录</option>
                    </select>
                </label>
                <label>状态
                    <select name="status">
                        <option value="draft" <?= $api['status'] === 'draft' ? 'selected' : '' ?>>草稿</option>
                        <option value="published" <?= $api['status'] === 'published' ? 'selected' : '' ?>>已发布</option>
                        <option value="disabled" <?= $api['status'] === 'disabled' ? 'selected' : '' ?>>已禁用</option>
                    </select>
                </label>
                <label class="check-toggle require-key-toggle span-3">
                    <input type="checkbox" name="require_key" value="1" <?= !empty($api['require_key']) ? 'checked' : '' ?>>
                    <span>
                        <strong>调用该 API 时必须传入有效的 <code>key</code> 参数</strong>
                        <small>开启后，调用方需要在请求参数中提供已审核通过的 UUID Key。</small>
                    </span>
                </label>
                <div class="method-box span-3">
                    <span>请求方式</span>
                    <?php $methods = array_map('trim', explode(',', $api['method_set'])); ?>
                    <label><input type="checkbox" name="methods[]" value="GET" <?= in_array('GET', $methods, true) ? 'checked' : '' ?>> GET</label>
                    <label><input type="checkbox" name="methods[]" value="POST" <?= in_array('POST', $methods, true) ? 'checked' : '' ?>> POST</label>
                </div>
            </div>
        </section>

        <section class="form-section table-section">
            <div class="form-section-title with-action">
                <span>04</span>
                <div>
                    <h2>请求参数说明</h2>
                    <p>用于生成接口详情页的请求参数表。</p>
                </div>
                <button type="button" class="secondary-btn" onclick="addParamRow()">+ 添加请求参数</button>
            </div>
            <div class="form-table-scroll">
                <table class="data-table form-param-table" id="params-table">
                    <thead><tr><th>参数名</th><th>必填</th><th>类型</th><th>说明</th><th>示例值</th><th></th></tr></thead>
                    <tbody>
                    <?php $rows = $params ?: [['param_name'=>'', 'required'=>0, 'param_type'=>'string', 'description'=>'', 'example_value'=>'']]; ?>
                    <?php foreach ($rows as $i => $p): ?>
                        <tr>
                            <td><input name="params[<?= $i ?>][param_name]" value="<?= h($p['param_name']) ?>" placeholder="url"></td>
                            <td class="check-cell"><input type="checkbox" name="params[<?= $i ?>][required]" value="1" <?= !empty($p['required']) ? 'checked' : '' ?>></td>
                            <td><input name="params[<?= $i ?>][param_type]" value="<?= h($p['param_type']) ?>" placeholder="string"></td>
                            <td><input name="params[<?= $i ?>][description]" value="<?= h($p['description']) ?>" placeholder="参数说明"></td>
                            <td><input name="params[<?= $i ?>][example_value]" value="<?= h($p['example_value']) ?>" placeholder="示例值"></td>
                            <td><button type="button" class="small-btn" onclick="this.closest('tr').remove()">删除</button></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="form-section table-section">
            <div class="form-section-title with-action">
                <span>05</span>
                <div>
                    <h2>返回参数说明</h2>
                    <p>用于补充“返回示例”下方的字段解释。</p>
                </div>
                <button type="button" class="secondary-btn" onclick="addResponseParamRow()">+ 添加返回参数</button>
            </div>
            <div class="form-table-scroll">
                <table class="data-table form-param-table" id="response-params-table">
                    <thead><tr><th>参数名</th><th>类型</th><th>说明</th><th>示例值</th><th></th></tr></thead>
                    <tbody>
                    <?php $responseRows = $responseParams ?: [
                        ['param_name'=>'code', 'param_type'=>'integer', 'description'=>'业务状态码，0 表示请求成功。', 'example_value'=>'0'],
                        ['param_name'=>'msg', 'param_type'=>'string', 'description'=>'接口调用结果说明。', 'example_value'=>'success'],
                        ['param_name'=>'data', 'param_type'=>'object', 'description'=>'接口返回的业务数据。', 'example_value'=>'{}'],
                    ]; ?>
                    <?php foreach ($responseRows as $i => $p): ?>
                        <tr>
                            <td><input name="response_params[<?= $i ?>][param_name]" value="<?= h($p['param_name']) ?>" placeholder="data.url"></td>
                            <td><input name="response_params[<?= $i ?>][param_type]" value="<?= h($p['param_type']) ?>" placeholder="string"></td>
                            <td><input name="response_params[<?= $i ?>][description]" value="<?= h($p['description']) ?>" placeholder="字段说明"></td>
                            <td><input name="response_params[<?= $i ?>][example_value]" value="<?= h($p['example_value']) ?>" placeholder="示例值"></td>
                            <td><button type="button" class="small-btn" onclick="this.closest('tr').remove()">删除</button></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <div class="form-actions sticky-actions">
            <button class="primary-btn" type="submit">保存 API</button>
            <a class="secondary-btn" href="/admin/apis">取消</a>
        </div>
    </form>
</main>
<script>
let paramIndex = <?= count($rows) ?>;
let responseParamIndex = <?= count($responseRows) ?>;
function addParamRow() {
    const tbody = document.querySelector('#params-table tbody');
    const i = paramIndex++;
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td><input name="params[${i}][param_name]" placeholder="url"></td>
        <td class="check-cell"><input type="checkbox" name="params[${i}][required]" value="1"></td>
        <td><input name="params[${i}][param_type]" value="string"></td>
        <td><input name="params[${i}][description]" placeholder="参数说明"></td>
        <td><input name="params[${i}][example_value]" placeholder="示例值"></td>
        <td><button type="button" class="small-btn" onclick="this.closest('tr').remove()">删除</button></td>`;
    tbody.appendChild(tr);
}
function addResponseParamRow() {
    const tbody = document.querySelector('#response-params-table tbody');
    const i = responseParamIndex++;
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td><input name="response_params[${i}][param_name]" placeholder="data.url"></td>
        <td><input name="response_params[${i}][param_type]" value="string"></td>
        <td><input name="response_params[${i}][description]" placeholder="字段说明"></td>
        <td><input name="response_params[${i}][example_value]" placeholder="示例值"></td>
        <td><button type="button" class="small-btn" onclick="this.closest('tr').remove()">删除</button></td>`;
    tbody.appendChild(tr);
}
</script>
<?php include __DIR__ . '/../partials/footer.php'; ?>
</body>
</html>

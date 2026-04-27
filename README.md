# 小锋学长的 API Hub

一个轻量级 PHP API 管理平台，用于集中展示、发布和管理自定义 API。项目采用本地文件数据存储，API 脚本统一放在 `api/` 目录下，通过统一网关暴露为 `/api/xxx` 调用地址。

> 在线体验：https://api.xfxuezhang.cn/

<img width="3338" height="1746" alt="image" src="https://github.com/user-attachments/assets/ec0be7f7-a171-4b8e-952f-3a3dce7c5499" />

<img width="3310" height="1710" alt="image" src="https://github.com/user-attachments/assets/a5448f44-e456-444f-8866-7a4db05a6648" />


## 功能特性

- 前台 API 列表展示
- API 详情文档页
- 请求参数说明
- 返回示例展示
- 返回参数说明
- 后台 API 新增、编辑、禁用、删除
- 支持上传 API 脚本文件
- 支持新增和选择接口分类
- 支持免费、付费、登录三种调用权限
- 支持累计调用、今日调用、创建时间展示
- API 脚本统一由网关加载
- 本地文件数据存储，便于快速部署和迁移

## 技术栈

- PHP
- HTML
- CSS
- JavaScript
- 本地 JSON 数据文件

## 项目结构

```text
project/
├─ api/                         # API 脚本目录
│  ├─ ip_query.php
│  └─ 163_music.php
├─ app/                         # 公共逻辑
│  ├─ auth.php
│  ├─ db.php
│  └─ helpers.php
├─ database/                    # 本地数据与初始化脚本
│  ├─ data.json
│  ├─ init.php
│  └─ schema.sql
├─ public/                      # Web 入口目录
│  ├─ admin.php
│  ├─ api_gateway.php
│  ├─ index.php
│  ├─ router.php
│  └─ assets/
├─ views/                       # 页面模板
├─ config.php
└─ README.md
```

## 快速开始

### 1. 解压项目

将项目解压到本地目录，例如：

```bash
cd api-hub
```

### 2. 初始化数据

```bash
php database/init.php
```

初始化后会生成或重置本地数据文件：

```text
database/data.json
```

### 3. 启动开发服务

```bash
php -S 127.0.0.1:8080 -t public public/router.php
```

### 4. 访问项目

```text
前台首页：
http://127.0.0.1:8080/

后台登录：
http://127.0.0.1:8080/admin/login

接口详情示例：
http://127.0.0.1:8080/api-doc/ip_query

接口调用示例：
http://127.0.0.1:8080/api/ip_query?ip=8.8.8.8
```

## 默认后台账号

```text
账号：admin
密码：admin123
```

首次部署后建议尽快修改默认密码。

## API 脚本规范

每个 API 脚本放在 `api/` 目录下，并返回一个可调用函数。

示例：

```php
<?php

return function (array $input): array {
    $ip = $input['ip'] ?? ($_SERVER['REMOTE_ADDR'] ?? '');

    if (!filter_var($ip, FILTER_VALIDATE_IP)) {
        throw new InvalidArgumentException('Invalid IP');
    }

    return [
        'ip' => $ip,
        'country' => '中国',
        'province' => '广东',
        'city' => '深圳',
        'isp' => '示例运营商',
    ];
};
```

后台新增 API 时上传该脚本文件，并设置路由，例如：

```text
路由：ip_query
脚本文件：ip_query.php
```

用户即可通过以下地址调用：

```text
/api/ip_query
```

## 新增 API 流程

1. 登录后台
2. 进入 API 管理
3. 点击新增 API
4. 填写 API 名称、路由、接口说明等基础信息
5. 上传对应的 PHP 脚本文件
6. 选择或新增分类
7. 选择调用权限
8. 填写请求参数说明
9. 填写返回参数说明
10. 保存为草稿或发布

发布后，API 会自动显示在前台首页和接口详情页。

## 图标规则

后台新增 API 时，图标标识可以为空。

- 如果为空，前台会使用 API 名称的第一个字作为默认图标
- 如果填写图片链接或本地路径，前台会使用提供的图片作为图标

示例：

```text
/assets/icons/weather.png
https://example.com/icon.png
```

## 调用权限

当前支持三种调用权限：

| 权限 | 说明 |
|---|---|
| 免费 | 无需付费即可调用 |
| 付费 | 需要付费或授权后调用 |
| 登录 | 需要登录后调用 |

当前版本主要用于展示和配置，实际鉴权逻辑可以按业务继续扩展。

## 返回参数说明

API 详情页支持展示返回参数说明，适合描述接口响应结构。

示例：

| 参数名 | 类型 | 说明 | 示例值 |
|---|---|---|---|
| code | integer | 状态码 | 0 |
| msg | string | 状态说明 | success |
| data | object | 返回数据主体 | {} |

## 本地数据说明

当前版本使用本地 JSON 文件保存数据，路径为：

```text
database/data.json
```

适合本地开发、小型站点、演示项目或早期 MVP。

如果后续访问量增大，可以迁移到 MySQL、SQLite 或 PostgreSQL。

## 安全建议

- 部署后请修改默认后台密码
- 不要允许不可信用户上传 PHP 脚本
- `api/` 目录内的脚本应由可信开发者维护
- 后台建议放在内网、管理端域名或加额外访问控制
- 生产环境建议使用 Nginx 或 Apache，不建议直接使用 PHP 内置服务
- 上传脚本前建议做文件类型、文件名和内容审查
- 接口参数应在脚本内再次校验

## 生产部署建议

开发环境可以使用 PHP 内置服务：

```bash
php -S 127.0.0.1:8080 -t public public/router.php
```

生产环境建议使用 Nginx 或 Apache，并将站点根目录指向 `public/`。

Nginx 伪配置示例：

```nginx
server {
    listen 80;
    server_name example.com;

    root /path/to/project/public;
    index index.php;

    location / {
        try_files $uri $uri/ /router.php?$query_string;
    }

    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }
}
```

## 常见问题

### 1. 为什么访问 `/api/xxx` 不是直接访问脚本文件？

项目通过统一网关 `public/api_gateway.php` 加载脚本。这样可以统一处理路由、权限、参数校验、调用日志和错误格式。

### 2. API 脚本上传后保存在哪里？

上传后的脚本统一保存到：

```text
api/
```

### 3. 为什么首页调用数据没有变化？

调用统计依赖通过 `/api/xxx` 网关访问。直接执行脚本文件不会记录调用日志。

### 4. 能否改成数据库版本？

可以。当前数据访问已集中在公共逻辑中，后续可以将本地 JSON 存储迁移为 SQLite 或 MySQL。

## 后续规划

- API Key 管理
- 登录态调用鉴权
- 付费接口授权
- 接口限流
- 接口分组排序
- 在线接口调试
- API 调用日志详情
- 错误率统计
- 多管理员账号
- 操作审计日志
- 数据库存储版本

## License
MIT License。

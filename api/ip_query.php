<?php

return function (array $input): array {
    $ip = $input['ip'] ?? ($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1');

    if (!filter_var($ip, FILTER_VALIDATE_IP)) {
        throw new InvalidArgumentException('Invalid IP');
    }

    $demo = [
        '8.8.8.8' => ['country' => '美国', 'province' => 'California', 'city' => 'Mountain View', 'isp' => 'Google LLC'],
        '1.1.1.1' => ['country' => '澳大利亚', 'province' => 'Queensland', 'city' => 'Brisbane', 'isp' => 'Cloudflare'],
        '127.0.0.1' => ['country' => '本机', 'province' => 'Local', 'city' => 'Localhost', 'isp' => 'Loopback'],
    ];

    return array_merge([
        'ip' => $ip,
        'country' => '中国',
        'province' => '广东',
        'city' => '深圳',
        'isp' => '示例运营商',
    ], $demo[$ip] ?? []);
};

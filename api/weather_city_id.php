<?php

return function (array $input): array {
    $city = trim((string)($input['city'] ?? ''));
    if ($city === '') {
        throw new InvalidArgumentException('city is required');
    }

    $map = [
        '北京' => '101010100',
        '上海' => '101020100',
        '广州' => '101280101',
        '深圳' => '101280601',
        '杭州' => '101210101',
    ];

    return [
        'city' => $city,
        'weather_id' => $map[$city] ?? '000000000',
        'matched' => isset($map[$city]),
    ];
};

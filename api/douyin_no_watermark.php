<?php

return function (array $input): array {
    $url = trim((string)($input['url'] ?? ''));
    if ($url === '') {
        throw new InvalidArgumentException('url is required');
    }

    return [
        'source_url' => $url,
        'title' => '抖音去水印解析演示',
        'video_url' => 'https://example.com/douyin-demo.mp4',
        'images' => [],
    ];
};

<?php

return function (array $input): array {
    $url = trim((string)($input['url'] ?? ''));
    if ($url === '') {
        throw new InvalidArgumentException('url is required');
    }

    return [
        'source_url' => $url,
        'title' => 'bilibili解析演示',
        'quality' => '1080P',
        'video_url' => 'https://example.com/bilibili-demo.mp4',
    ];
};

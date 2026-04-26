<?php

return function (array $input): array {
    $url = trim((string)($input['url'] ?? ''));
    if ($url === '') {
        throw new InvalidArgumentException('url is required');
    }

    return [
        'source_url' => $url,
        'title' => '豆包视频解析演示',
        'cover' => 'https://example.com/cover.jpg',
        'video_url' => 'https://example.com/video.mp4',
        'notice' => '这是演示返回，请替换真实解析逻辑。',
    ];
};

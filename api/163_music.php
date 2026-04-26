<?php

return function (array $input): array {
    $id = trim((string)($input['id'] ?? ''));
    $url = trim((string)($input['url'] ?? ''));

    if ($id === '' && $url === '') {
        throw new InvalidArgumentException('id or url is required');
    }

    if ($id === '' && preg_match('/id=([0-9]+)/', $url, $matches)) {
        $id = $matches[1];
    }

    return [
        'id' => $id ?: 'demo',
        'title' => '示例歌曲',
        'artist' => '示例歌手',
        'album' => '示例专辑',
        'quality' => 'SVIP Demo',
        'play_url' => 'https://example.com/music/' . ($id ?: 'demo') . '.mp3',
        'notice' => '这是本地演示数据，请替换 api/163_music.php 为真实解析逻辑。',
    ];
};

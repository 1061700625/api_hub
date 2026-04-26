<?php

return function (array $input): array {
    $timestamp = $input['timestamp'] ?? null;
    $datetime = trim((string)($input['datetime'] ?? ''));

    if ($timestamp !== null && $timestamp !== '') {
        $timestamp = (int)$timestamp;
        return [
            'timestamp' => $timestamp,
            'datetime' => date('Y-m-d H:i:s', $timestamp),
        ];
    }

    if ($datetime !== '') {
        $time = strtotime($datetime);
        if ($time === false) {
            throw new InvalidArgumentException('Invalid datetime');
        }
        return [
            'datetime' => date('Y-m-d H:i:s', $time),
            'timestamp' => $time,
        ];
    }

    return [
        'timestamp' => time(),
        'datetime' => date('Y-m-d H:i:s'),
    ];
};

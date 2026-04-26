<?php

return function (array $input, array $context): array {
    return [
        'method' => $context['method'],
        'received' => $input,
        'time' => date('Y-m-d H:i:s'),
    ];
};

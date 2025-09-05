<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

return [
    'default' => [
        'handler' => [
            // 'class' => Monolog\Handler\StreamHandler::class,
            'class' => \Monolog\Handler\RotatingFileHandler::class,
            'constructor' => [
                // 'stream' => BASE_PATH . '/runtime/logs/stream/hyperf.log',
                'filename' => BASE_PATH . '/runtime/logs/stream/hyperf.log',
                'maxFiles' => 14,
                'level' => \Monolog\Level::Debug,
            ],
        ],
        'formatter' => [
            'class' => \Monolog\Formatter\LineFormatter::class,
            'constructor' => [
                'format' => null,
                'dateFormat' => 'Y-m-d H:i:s',
                'allowInlineLineBreaks' => true,
            ]
        ]
    ],
    'stdout' => [
        'handler' => [
            'class' => Monolog\Handler\RotatingFileHandler::class,
            'constructor' => [
                'filename' => BASE_PATH . '/runtime/logs/stdout/stdout.log',
                'maxFiles' => 14,
                'level' => \Monolog\Level::Debug,
            ],
            'formatter' => [
                'class' => \Monolog\Formatter\LineFormatter::class,
                'constructor' => [
                    'format' => "[%datetime%] %level_name%: %message% [IP: %extra.ip%]\n",
                    'dateFormat' => 'Y-m-d H:i:s',
                    'allowInlineLineBreaks' => true,
                ]
            ],
        ],
    ],
    'user' => [
        'handlers' => [
            [
                'class' => Monolog\Handler\RotatingFileHandler::class,
                'constructor' => [
                    'filename' => BASE_PATH . '/runtime/logs/user/info.log',
                    'maxFiles' => 14,
                    'level' => \Monolog\Level::Info,
                ],
                'formatter' => [
                    'class' => \Monolog\Formatter\LineFormatter::class,
                    'constructor' => [
                        'format' => null,
                        'dateFormat' => 'Y-m-d H:i:s',
                        'allowInlineLineBreaks' => true,
                    ],
                ],
            ],
            [
                'class' => Monolog\Handler\RotatingFileHandler::class,
                'constructor' => [
                    'filename' => BASE_PATH . '/runtime/logs/user/error.log',
                    'level' => \Monolog\Level::Error,
                ],
                'formatter' => [
                    'class' => \Monolog\Formatter\LineFormatter::class,
                    'constructor' => [
                        'dateFormat' => 'Y-m-d H:i:s',
                        'appendNewline' => true,
                    ],
                ],
            ],
        ],
    ],
    'order' => [
        'handlers' => [
            [
                'class' => Monolog\Handler\RotatingFileHandler::class,
                'constructor' => [
                    'filename' => BASE_PATH . '/runtime/logs/order/info.log',
                    'maxFiles' => 14,
                    'level' => \Monolog\Level::Info,
                ],
                'formatter' => [
                    'class' => \Monolog\Formatter\LineFormatter::class,
                    'constructor' => [
                        'format' => null,
                        'dateFormat' => 'Y-m-d H:i:s',
                        'allowInlineLineBreaks' => true,
                    ],
                ],
            ],
            [
                'class' => Monolog\Handler\RotatingFileHandler::class,
                'constructor' => [
                    'filename' => BASE_PATH . '/runtime/logs/order/error.log',
                    'level' => \Monolog\Level::Error,
                ],
                'formatter' => [
                    'class' => \Monolog\Formatter\LineFormatter::class,
                    'constructor' => [
                        'dateFormat' => 'Y-m-d H:i:s',
                        'appendNewline' => true,
                    ],
                ],
            ],
        ],
    ],
];

<?php

use Tequilarapido\Consolify\Progress\OriginalProgressBar;

return [

    'progress' => [

        /*
        |--------------------------------------------------------------------------
        | Concrete class handling progress.
        |
        |   - Tequilarapido\Consolify\Progress\NullProgress::class
        |          - Do nothing.
        |
        |   - Tequilarapido\Consolify\Progress\RedisProgress::class
        |          - Store progress in redis.
        |--------------------------------------------------------------------------
        */
        'concrete_class' => OriginalProgressBar::class,

        /*
        |--------------------------------------------------------------------------
        | When using RedisProgressBar::class progress values will be stored as object
        | in redis connection. You can choose a prefix to avoid collisions.
        |--------------------------------------------------------------------------
        */
        'redis_prefix' => 'consolify:progress:',
    ]

];
<?php

declare(strict_types=1);

namespace App\Service;

use Hyperf\Redis\Redis;

class OnlineUserService
{
    private string $key = 'online_user_count';

    public function __construct(
        protected Redis $redis
    ) {}
    public function getCount(): int
    {
        return (int) $this->redis->get($this->key) ?: 0;
        
    }
    public function increment(): int
    {
        return $this->redis->incr($this->key);
    }

    public function decrement(): int
    {
        $count = max(0, (int) $this->redis->decr($this->key));
        $this->redis->set($this->key, $count);
        return $count;
    }
}
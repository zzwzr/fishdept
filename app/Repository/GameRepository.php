<?php

declare(strict_types=1);

namespace App\Repository;

use Hyperf\Redis\Redis;

class GameRepository
{
    // romm hash details
    private $room = 'room:';

    // step
    private $step = 'step:';

    // move
    private $lockMove = 'lock:move:';

    public function __construct(
        private Redis $redis,
    ) {}

    public function initGameSteps(string $roomId, array $playerOrder): void
    {
        $stepKey = $this->step . $roomId;

        $this->redis->del($stepKey);

        // 正序进入，步骤为先入先出
        foreach ($playerOrder as $playerId) {
            $this->redis->lpush($stepKey, $playerId);
        }
    }

    public function lockMove(string $roomId): bool
    {
        return $this->redis->set($this->lockMove . $roomId, 1, ['NX', 'EX' => 2]);
    }

    public function unlockMove(string $roomId): void
    {
        $this->redis->del($this->lockMove . $roomId);
    }

    public function isCurrentPlayer(string $roomId, string $playerId): bool
    {
        $stepKey = $this->step . $roomId;
        $len = $this->redis->llen($stepKey);
        
        $listPlayerId = $this->redis->lindex($stepKey, $len - 1);

        if ($listPlayerId != $playerId) {
            return false;
        }
        return true;
    }
}
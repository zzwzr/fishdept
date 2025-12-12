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

    // board
    private $board = 'board:';

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

    public function initGameBoard(string $roomId): void
    {
        $boardKey = $this->board . $roomId;

        $this->redis->del($boardKey);

        // lpush 会反向，所以用 rpush 保证 0~8 顺序
        for ($i = 0; $i < 9; $i++) {
            $this->redis->rpush($boardKey, '');
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

    public function updateCell(string $roomId, int $index, string $value): void
    {
        $boardKey = $this->board . $roomId;

        $this->redis->lset($boardKey, $index, $value);
    }

    public function getBoard(string $roomId): array
    {
        $boardKey = $this->board . $roomId;
        return $this->redis->lrange($boardKey, 0, 8);
    }

    public function tryPlacePiece(string $roomId, int $index, string $symbol): bool
    {
        $boardKey = $this->board . $roomId;

        $current = $this->redis->lindex($boardKey, $index);

        if ($current !== '') return false;

        $this->redis->lset($boardKey, $index, $symbol);

        return true;
    }

    public function checkWinner(string $roomId): ?string
    {
        $boardKey = $this->board . $roomId;

        $board = $this->redis->lrange($boardKey, 0, 8);

        if (count(array_filter($board)) < 3) {
            return null;
        }

        $lines = [
            [0,1,2], [3,4,5], [6,7,8],
            [0,3,6], [1,4,7], [2,5,8],
            [0,4,8], [2,4,6]
        ];
        foreach ($lines as $line) {
            [$a, $b, $c] = $line;

            if ($board[$a] !== '' && $board[$a] === $board[$b] && $board[$b] === $board[$c]) {
                return $board[$a];
            }
        }

        return null;
    }

    public function rotateStep(string $roomId): string
    {
        $stepKey = $this->step . $roomId;
        $player = $this->redis->rpop($stepKey);
        $this->redis->lpush($stepKey, $player);
        return $player;
    }
}
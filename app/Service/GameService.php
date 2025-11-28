<?php

declare(strict_types=1);

namespace App\Service;

use App\Constants\ErrorCode;
use App\Exception\BusinessException;
use Hyperf\Redis\Redis;

class GameService
{
    // browser mapping roomid
    private $browserRoom = 'browser_room:';

    // romm hash details
    private $room = 'room:';

    // romm set list
    private $rooms = 'rooms:';

    // step
    private $step = 'step:';

    public function __construct(
        protected Redis $redis
    ) {}

    /**
     * @param string|bool $roomId
     * @return void
     */
    public function start($roomId): void
    {
        $roomKey = $this->room . $roomId;
        $stepKey = $this->step . $roomId;
        if (!$this->redis->exists($roomKey)) {
            throw new BusinessException(ErrorCode::BUSINESS_ERROR, '房间不存在');
        }

        $player1 = $this->redis->hGet($roomKey, 'player1');
        $player2 = $this->redis->hGet($roomKey, 'player2');

        if (!$player1 || !$player2) {
            throw new BusinessException(ErrorCode::BUSINESS_ERROR, '不满足开始条件');
        }
        $this->redis->hSet($roomKey, 'status', 2);

        // 设置玩家步骤
        $this->redis->lpush($stepKey, $player2, $player1);
    }
}
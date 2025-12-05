<?php

declare(strict_types=1);

namespace App\Service;

use App\Constants\ErrorCode;
use App\Exception\BusinessException;
use App\Repository\GameRepository;
use App\Repository\RoomRepository;

class GameService
{
    // romm hash details
    private $room = 'room:';

    public function __construct(
        protected RoomRepository $roomRepository,
        protected GameRepository $gameRepository,
    ) {}

    public function start($roomId): void
    {
        if (!$this->roomRepository->existsRoom($roomId)) {
            throw new BusinessException(ErrorCode::BUSINESS_ERROR, '房间不存在');
        }

        $p1 = $this->roomRepository->getRoomField($roomId, 'player1');
        $p2 = $this->roomRepository->getRoomField($roomId, 'player2');

        if (!$p1 || !$p2) {
            throw new BusinessException(ErrorCode::BUSINESS_ERROR, '不满足开始条件');
        }

        $this->roomRepository->setRoomField($roomId, 'status', 2);

        $this->gameRepository->initGameSteps($roomId, [$p1, $p2]);
    }

    public function move(array $validated)
    {
        var_dump($validated);
        // 1.处理同用户同时多个请求的情况，2.检查玩家是否存在于当前房间，3.检查游戏状态是否在游戏中，4.检查回合是否正确，5.检查落子是否正确，6.检查是否胜利，7.更新下一回合，8.记录步骤

        // 步骤锁，防止步骤未完成的时候，又走了一步，导致连落两子
        if (!$this->gameRepository->lockMove($validated['room_id'])) {
            throw new BusinessException(ErrorCode::BUSINESS_ERROR, '慢点儿 慢点儿！');
        }

        $p1 = $this->roomRepository->getRoomField($validated['room_id'], 'player1');
        $p2 = $this->roomRepository->getRoomField($validated['room_id'], 'player2');
        if ($p1 != $validated['browser_id'] && $p2 != $validated['browser_id']) {
            throw new BusinessException(ErrorCode::BUSINESS_ERROR, '咱们不在一个房间哦！');
        }

        $status = $this->roomRepository->getRoomField($validated['room_id'], 'status');
        if (2 != $status) {
            throw new BusinessException(ErrorCode::BUSINESS_ERROR, '还没开始呢 哥哥~');
        }

        if (!$this->gameRepository->isCurrentPlayer($validated['room_id'], $validated['browser_id'])) {
            throw new BusinessException(ErrorCode::BUSINESS_ERROR, '哎~ 别急嘛，等一下人家');
        }


        $this->gameRepository->unlockMove($validated['room_id']);
    }
}
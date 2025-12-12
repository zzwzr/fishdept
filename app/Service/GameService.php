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
        $this->roomRepository->setRoomHash($roomId, [$p1 => 'X', $p2 => 'O']);

        $this->gameRepository->initGameSteps($roomId, [$p1, $p2]);
    }

    public function move(array $validated)
    {
        // 1.处理同用户同时多个请求的情况，2.检查玩家是否存在于当前房间，3.检查游戏状态是否在游戏中，4.检查回合是否正确，5.检查落子是否正确，6.落子，7.检查是否胜利，8.更新下一回合，9.记录步骤

        // 步骤锁，防止步骤未完成的时候，又走了一步，导致连落两子
        if (!$this->gameRepository->lockMove($validated['room_id'])) {
            throw new BusinessException(ErrorCode::BUSINESS_ERROR, '个人服务器，莫要点那么快哦！');
        }

        $p1 = $this->roomRepository->getRoomField($validated['room_id'], 'player1');
        $p2 = $this->roomRepository->getRoomField($validated['room_id'], 'player2');
        if ($p1 != $validated['browser_id'] && $p2 != $validated['browser_id']) {
            throw new BusinessException(ErrorCode::BUSINESS_ERROR, '不在房间！');
        }

        $status = $this->roomRepository->getRoomField($validated['room_id'], 'status');
        if (2 != $status) {
            throw new BusinessException(ErrorCode::BUSINESS_ERROR, '未开始！');
        }

        if (!$this->gameRepository->isCurrentPlayer($validated['room_id'], $validated['browser_id'])) {
            throw new BusinessException(ErrorCode::BUSINESS_ERROR, '未到您的回合！');
        }

        $symbol = $this->roomRepository->getRoomField($validated['room_id'], $validated['browser_id']);

        if (!$this->gameRepository->tryPlacePiece($validated['room_id'], $validated['index'], $symbol)) {
            throw new BusinessException(ErrorCode::BUSINESS_ERROR, '已存在');
        }

        if (null == $this->gameRepository->checkWinner($validated['room_id'])) {
            throw new BusinessException(ErrorCode::BUSINESS_ERROR, '胜利了');
        }

        $this->gameRepository->rotateStep($validated['room_id']);

        // 记录日志

        // 步骤锁，解锁
        $this->gameRepository->unlockMove($validated['room_id']);
    }
}
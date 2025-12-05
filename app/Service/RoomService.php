<?php

declare(strict_types=1);

namespace App\Service;

use App\Constants\ErrorCode;
use App\Exception\BusinessException;
use App\Repository\RoomRepository;

class RoomService
{
    public function __construct(private RoomRepository $roomRepository) {}

    public function createRoom(string $name, string $browserId): string
    {
        $roomId = generateRandomCode();

        $this->roomRepository->createRoomHash($roomId, [
            'name'          => $name,
            'player1'       => '',
            'player2'       => '',
            'status'        => 1,
            'created_at'    => date('Y-m-d H:i:s'),
        ]);

        $this->roomRepository->addRoomToSet($roomId);
        $this->joinRoom($roomId, $browserId);

        return $roomId;
    }

    public function joinRoom(string $roomId, string $browserId): void
    {
        if (!$this->roomRepository->existsRoom($roomId)) {
            throw new BusinessException(ErrorCode::BUSINESS_ERROR, '房间不存在');
        }

        $old = $this->roomRepository->getBrowserRoom($browserId);
        if ($old === $roomId) {
            throw new BusinessException(ErrorCode::BUSINESS_ERROR, '已在该房间');
        }

        if ($old) $this->leaveRoom($old, $browserId);

        $p1 = $this->roomRepository->getRoomField($roomId, 'player1');
        $p2 = $this->roomRepository->getRoomField($roomId, 'player2');

        if ($p1 === '') {
            $this->roomRepository->setRoomField($roomId, 'player1', $browserId);
        } elseif ($p2 === '') {
            $this->roomRepository->setRoomField($roomId, 'player2', $browserId);
        } else {
            throw new BusinessException(ErrorCode::BUSINESS_ERROR, '房间已满');
        }

        $this->roomRepository->setBrowserRoom($browserId, $roomId);
    }

    public function listRooms(string $search = ''): array
    {
        $ids = $this->roomRepository->getAllRoomIds();
        $result = [];

        foreach ($ids as $id) {
            $info = $this->infoRoom($id);
            if (!$info) continue;
            if ($search && !str_contains($info['name'], $search)) continue;
            $result[] = $info;
        }

        return $result;
    }

    public function infoRoom(string $roomId): array
    {
        $data = $this->roomRepository->getRoomHash($roomId);
        if (empty($data['name'])) {
            return [];
        }

        $count = ($data['player1'] ? 1 : 0) + ($data['player2'] ? 1 : 0);

        return [
            'number'        => $roomId,
            'name'          => $data['name'],
            'player1'       => $data['player1'],
            'player2'       => $data['player2'],
            'count'         => $count,
            'status'        => $data['status'],
            'created_at'    => $data['created_at']
        ];
    }

    public function firstRoom(string $browserId): array
    {
        $roomId = $this->roomRepository->getBrowserRoom($browserId);
        if (!$roomId) return [];

        return $this->infoRoom($roomId);
    }

    public function leaveRoom(string $roomId, string $browserId): void
    {
        if (!$this->roomRepository->existsRoom($roomId)) return;

        $keys = $this->roomRepository->getKeys($roomId);

        $ret = $this->roomRepository->runLeaveRoomLua(
            $this->leaveRoomLua(),
            // [$roomKey, $roomId, $this->rooms, $step, $browserId, $this->browserRoom . $browserId]
            array_merge($keys, [$browserId, $this->roomRepository->browserRoomKey($browserId)])
        );

        // if ($ret === 1) {
            // deleted
        // }
    }

    /**
     * 避免出现检查两个玩家都不在，未执行删除房间之前，又有人进入房间了的情况
     * @return string
     */
    private function leaveRoomLua(): string
    {
        return <<<'LUA'
local roomKey = KEYS[1]
local roomId = KEYS[2]
local rooms = KEYS[3]
local step = KEYS[4]

local browserId = ARGV[1]
local browserRoomKey = ARGV[2]

redis.call("DEL", browserRoomKey)

local p1 = redis.call("HGET", roomKey, "player1")
local p2 = redis.call("HGET", roomKey, "player2")

if p1 == browserId then
    redis.call("HSET", roomKey, "player1", "")
elseif p2 == browserId then
    redis.call("HSET", roomKey, "player2", "")
end

p1 = redis.call("HGET", roomKey, "player1")
p2 = redis.call("HGET", roomKey, "player2")

if (not p1 or p1 == "") and (not p2 or p2 == "") then
    redis.call("DEL", roomKey)
    redis.call("SREM", rooms, roomId)
    redis.call("DEL", step)
    return 1
end

redis.call("HSET", roomKey, "status", "1")
return 2
LUA;
    }
}
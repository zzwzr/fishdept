<?php

declare(strict_types=1);

namespace App\Service;

use App\Constants\ErrorCode;
use App\Exception\BusinessException;
use Hyperf\Redis\Redis;

class RoomService
{
    // browser mapping roomid
    private $browserRoom = 'browser_room:';

    // romm hash details
    private $room = 'room:';

    // romm set list
    private $rooms = 'rooms:';

    public function __construct(
        protected Redis $redis
    ) {}

    public function createRoom(string $roomName, string $browserId = ''): string
    {
        $roomId = generateRandomCode();
        $key = $this->room . $roomId;

        $this->redis->hMSet($key, [
            'name'          => $roomName,
            'player1'       => '',
            'player2'       => '',
            'status'        => 'waiting',
            'created_at'    => date('Y-m-d H:i:s')
        ]);

        $this->redis->sAdd($this->rooms, $roomId);

        $this->joinRoom($roomId, $browserId);
        return $roomId;
    }

    public function joinRoom(string $roomId, string $browserId): void
    {
        $roomKey = $this->room . $roomId;
        if (!$this->redis->exists($roomKey)) {
            throw new BusinessException(ErrorCode::BUSINESS_ERROR, '房间不存在！');
        }

        $oldRoomId = $this->redis->get($this->browserRoom . $browserId);

        if ($oldRoomId == $roomId) {
            throw new BusinessException(ErrorCode::BUSINESS_ERROR, '已在该房间！');
        } else {

            $this->leaveRoom($oldRoomId, $browserId);

            $player1 = $this->redis->hGet($roomKey, 'player1');
            $player2 = $this->redis->hGet($roomKey, 'player2');

            if (empty($player1)) {
                $this->redis->hSet($roomKey, 'player1', $browserId);
            } elseif (empty($player2)) {
                $this->redis->hSet($roomKey, 'player2', $browserId);
                $this->redis->hSet($roomKey, 'status', 'ready');
            } else {
                throw new BusinessException(ErrorCode::BUSINESS_ERROR, '房间已满员');
            }

            $this->redis->set($this->browserRoom . $browserId, $roomId);
        }
    }

    public function listRooms($search = ''): array
    {
        $roomIds = $this->redis->sMembers($this->rooms);
        $rooms = [];

        if ($search) {
            $rooms[] = $this->rooms($search);
        } else {
            foreach ($roomIds as $roomId) {
                $rooms[] = $this->rooms($roomId);
            }
        }
        return $rooms;
    }

    public function infoRoom($roomId): array
    {
        return $this->rooms($roomId);
    }

    public function firstRoom(string $browserId): array
    {
        $roomId = $this->browserIdGetRoomId($browserId);
        if (!$roomId) return [];
        return $this->rooms($roomId);
    }

    /**
     * browserId get room id
     * @param string $browserId
     * @return string
     */
    public function browserIdGetRoomId(string $browserId): string
    {
        return $this->redis->get($this->browserRoom . $browserId) ?: '';
    }

    /**
     * 离开 room
     * @param string|bool $roomId
     * @param string $browserId
     * @return void
     */
    public function leaveRoom($roomId, string $browserId): void
    {
        $roomKey = $this->room . $roomId;
        if (!$this->redis->exists($roomKey)) {
            return;
        }

        $lua = $this->leaveRoomLua();
        $this->redis->eval($lua, [$roomKey, $roomId, $browserId, $this->browserRoom . $browserId], 2);
    }

    /**
     * 查询房间
     * @param [type] $roomId 房间编号
     * @return array
     */
    private function rooms($roomId): array
    {
        $data = $this->redis->hMGet($this->room . $roomId, ['name', 'player1', 'player2', 'status', 'created_at']);
        if (empty($data['name'])) {
            $this->redis->sRem($this->rooms, $roomId);
            return [];
        }

        $count = 0;
        if (!empty($data['player1'])) $count++;
        if (!empty($data['player2'])) $count++;

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

    /**
     * 离开 room LUA 脚本组成（主要阻止检查了p1p2没有用户之后，未删除之前有人加进来了）
     */
    private function leaveRoomLua(): string
    {
        return <<<'LUA'
local roomKey = KEYS[1]
local roomId = KEYS[2]
local browserId = ARGV[1]
local browserRoomKey = ARGV[2]

-- 删除浏览器和房间的映射关系
redis.call("DEL", browserRoomKey)

local player1 = redis.call("HGET", roomKey, "player1")
local player2 = redis.call("HGET", roomKey, "player2")

if player1 == browserId then
    redis.call("HSET", roomKey, "player1", "")
elseif player2 == browserId then
    redis.call("HSET", roomKey, "player2", "")
end

-- 重新读取，判断是否都为空或 nil
player1 = redis.call("HGET", roomKey, "player1")
player2 = redis.call("HGET", roomKey, "player2")

if (not player1 or player1 == "") and (not player2 or player2 == "") then
    redis.call("DEL", roomKey)
    redis.call("SREM", "rooms:", roomId)
    return 1 -- 房间删除
end

redis.call("HSET", roomKey, "status", "waiting")
return 2 -- 房间还有一个人
LUA;
    }
}
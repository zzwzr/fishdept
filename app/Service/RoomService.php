<?php

declare(strict_types=1);

namespace App\Service;

use Hyperf\Redis\Redis;

class RoomService
{
    public function __construct(
        protected Redis $redis
    ) {}

    /**
     * 创建房间
     */
    public function createRoom(string $roomName, string $browserId = ''): string
    {
        $roomId = generateRandomCode();
        $key = "room:{$roomId}";

        // 初始化房间数据
        $this->redis->hMSet($key, [
            'name'          => $roomName,
            'player1'       => $browserId,
            'player2'       => '',
            'status'        => 'waiting',
            'created_at'    => date('Y-m-d H:i:s')
        ]);

        $this->redis->sAdd('rooms', $roomId);

        // 额外建立浏览器ID到房间的映射
        $this->redis->set("browser_room:{$browserId}", $roomId);
        return $roomId;
    }

    /**
     * 获取房间列表
     */
    public function listRooms($search = ''): array
    {
        $roomIds = $this->redis->sMembers('rooms');
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

    /**
     * 获取当前浏览器是否在房间中
     * @param string $browserId
     * @return array|null
     */
    public function getRoomByBrowserId(string $browserId): ?array
    {
        $roomId = $this->redis->get("browser_room:{$browserId}");
        if (!$roomId) {
            return null;
        }

        $data = $this->redis->hGetAll("room:{$roomId}");
        if (empty($data)) {
            $this->redis->del("browser_room:{$browserId}");
            return [];
        }

        $data['room_id'] = $roomId;
        return $data;
    }

    /**
     * 查询房间
     * @param [type] $roomId 房间编号
     * @return array
     */
    private function rooms($roomId): array
    {
        $data = $this->redis->hMGet("room:{$roomId}", ['name', 'player1', 'player2', 'status']);
        if (empty($data['name'])) {
            $this->redis->sRem('rooms', $roomId);
            return [];
        }

        $count = 0;
        if (!empty($data['player1'])) $count++;
        if (!empty($data['player2'])) $count++;

        return [
            'number'    => $roomId,
            'name'      => $data['name'],
            'count'     => $count,
            'status'    => $data['status'],
        ];
    }

    /**
     * 加入房间
     */
    public function joinRoom(string $roomId, string $userId): bool
    {
        $key = "room:{$roomId}";
        if (!$this->redis->exists($key)) {
            return false;
        }

        $player1 = $this->redis->hGet($key, 'player1');
        $player2 = $this->redis->hGet($key, 'player2');

        if (empty($player1)) {
            $this->redis->hSet($key, 'player1', $userId);
        } elseif (empty($player2)) {
            $this->redis->hSet($key, 'player2', $userId);
            $this->redis->hSet($key, 'status', 'ready');
        } else {
            // 房间已满
            return false;
        }

        return true;
    }
}
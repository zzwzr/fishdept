<?php

declare(strict_types=1);

namespace App\Service;

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
            'player1'       => $browserId,
            'player2'       => '',
            'status'        => 'waiting',
            'created_at'    => date('Y-m-d H:i:s')
        ]);

        $this->redis->sAdd($this->rooms, $roomId);

        $this->redis->set($this->browserRoom . $browserId, $roomId);
        return $roomId;
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

    public function joinRoom(string $roomId, string $browserId): bool
    {
        $roomKey = "room:{$roomId}";
        if (! $this->redis->exists($roomKey)) {
            return false;
        }
        $userRoomKey = $this->browserRoom . $browserId;

        $oldRoomId = $this->redis->get($userRoomKey);
        if ($oldRoomId && $oldRoomId !== $roomId) {
            $this->leaveRoom($oldRoomId, $browserId);
        }

        $player1 = $this->redis->hGet($roomKey, 'player1');
        $player2 = $this->redis->hGet($roomKey, 'player2');

        if (empty($player1)) {
            $this->redis->hSet($roomKey, 'player1', $browserId);
        } elseif (empty($player2)) {
            $this->redis->hSet($roomKey, 'player2', $browserId);
            $this->redis->hSet($roomKey, 'status', 'ready');
        } else {
            return false;
        }

        // 4. 更新映射
        $this->redis->set($userRoomKey, $roomId);

        return true;
    }

    public function leaveRoom(string $roomId, string $browserId): void
    {
        $roomKey = "room:{$roomId}";
        if (! $this->redis->exists($roomKey)) {
            return;
        }

        $player1 = $this->redis->hGet($roomKey, 'player1');
        $player2 = $this->redis->hGet($roomKey, 'player2');

        if ($player1 === $browserId) {
            $this->redis->hSet($roomKey, 'player1', '');
        }
        if ($player2 === $browserId) {
            $this->redis->hSet($roomKey, 'player2', '');
        }

        if (empty($player1) && empty($player2)) {

            $this->redis->hSet($roomKey, 'status', 'waiting');
        }

        // 删除用户的映射关系
        $this->redis->del("user_room:{$browserId}");
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
}
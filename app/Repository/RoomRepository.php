<?php

declare(strict_types=1);

namespace App\Repository;

use Hyperf\Redis\Redis;

class RoomRepository
{
    // browser mapping roomid
    private $browserRoom = 'browser_room:';

    // romm hash details
    private $room = 'room:';

    // romm set list
    private $rooms = 'rooms:';

    // step
    private $step = 'step:';

    public function __construct(private Redis $redis) {}

    public function createRoomHash(string $roomId, array $data): void
    {
        $this->redis->hMSet($this->room . $roomId, $data);
    }

    public function addRoomToSet(string $roomId): void
    {
        $this->redis->sAdd($this->rooms, $roomId);
    }

    public function setBrowserRoom(string $browserId, string $roomId): void
    {
        $this->redis->set($this->browserRoom . $browserId, $roomId);
    }

    public function getBrowserRoom(string $browserId): string
    {
        return $this->redis->get($this->browserRoom . $browserId) ?: '';
    }

    public function getRoomHash(string $roomId): array
    {
        return $this->redis->hMGet(
            $this->room . $roomId,
            ['name', 'player1', 'player2', 'status', 'created_at']
        );
    }

    public function getRoomField(string $roomId, string $field): string
    {
        return $this->redis->hGet($this->room . $roomId, $field);
    }

    public function setRoomField(string $roomId, string $field, string|int $value): void
    {
        $this->redis->hSet($this->room . $roomId, $field, $value);
    }

    public function existsRoom(string $roomId): bool
    {
        return $this->redis->exists($this->room . $roomId) ? true : false;
    }

    public function getAllRoomIds(): array
    {
        return $this->redis->sMembers($this->rooms);
    }

    // 删除空房间
    public function deleteRoomCompletely(string $roomId): void
    {
        $this->redis->del($this->room . $roomId);
        $this->redis->sRem($this->rooms, $roomId);
        $this->redis->del($this->step . $roomId);
    }

    public function runLeaveRoomLua(string $lua, array $args): int
    {
        return $this->redis->eval($lua, $args, 4);
    }

    public function getKeys(string $roomId): array
    {
        return [
            $this->room . $roomId,
            $roomId,
            $this->rooms,
            $this->step . $roomId
        ];
    }

    public function browserRoomKey(string $browserId): string
    {
        return $this->browserRoom . $browserId;
    }
}

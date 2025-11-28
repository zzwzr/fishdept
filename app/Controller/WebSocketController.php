<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\OnlineUserService;
use App\Service\UserService;
use Hyperf\Contract\OnCloseInterface;
use Hyperf\Contract\OnMessageInterface;
use Hyperf\Contract\OnOpenInterface;
use Hyperf\Engine\WebSocket\Frame;
use Hyperf\Engine\WebSocket\Response;
use Hyperf\WebSocketServer\Constant\Opcode;

class WebSocketController implements OnMessageInterface, OnOpenInterface, OnCloseInterface
{
    public function __construct (
        protected ServerController $serverController,
        protected UserService $userService,
        protected OnlineUserService $onlineUserService,
    ) {}

    // 房间锁，步骤锁
    public function onOpen($server, $request): void
    {
        $fd = $request->fd;

        $browserId = $request->get['browser_id'] ?? null;
        if (!$browserId) {
            $this->serverController->send($fd, ['code' => 400, 'message' => '参数错误', 'data' => []]);
            $this->serverController->close($fd);
            return;
        }

        $this->onlineUserService->increment();

        // 获取用户
        // $user = $this->userService->findOrCreateByBrowserId($browserId);

        // 绑定fd
        

        $this->serverController->send($fd, ['code' => 0, 'message' => '连接成功', 'data' => []]);
    }

    public function onMessage($server, $frame): void
    {
        $response = (new Response($server))->init($frame);
        if($frame->opcode == Opcode::PING) {
            $response->push(new Frame(opcode: Opcode::PONG));
            return;
        }
        $response->push(new Frame(payloadData: 'Recv: ' . $frame->data));
    }

    public function onClose($server, int $fd, int $reactorId): void
    {
        // 在线人数
        $this->onlineUserService->decrement();

        var_dump('closed');
    }
}

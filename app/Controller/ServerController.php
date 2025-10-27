<?php

declare(strict_types=1);

namespace App\Controller;

use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\WebSocketServer\Sender;
use function Hyperf\Coroutine\go;

#[AutoController]
class ServerController
{
    #[Inject]
    protected Sender $sender;

    public function close(int $fd): void
    {
        go(function () use ($fd) {
            sleep(1);
            $this->sender->disconnect($fd);
        });
    }

    /**
     * Undocumented function
     *
     * @param integer $fd
     * @param array $data
     * @return void
     */
    public function send(int $fd, array $data): void
    {
        // $data = [
        //     'type'   => 0,
        //     'data'   => 'success',
        // ];
        $this->sender->push($fd, json_encode($data));
    }
}

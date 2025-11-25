<?php

declare(strict_types=1);

namespace App\Command;

use App\Logger\UserLogger;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Command\Annotation\Command;
use Psr\Container\ContainerInterface;
use Hyperf\Coroutine\Coroutine;
use Hyperf\Guzzle\ClientFactory;

#[Command]
class DdosCommand extends HyperfCommand
{
    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('ddos');
    }

    public function configure()
    {
        parent::configure();
        $this->setDescription('Hyperf Demo Command');
    }

    public function handle()
    {
        $clientFactory = $this->container->get(ClientFactory::class);
        $client = $clientFactory->create([
            'base_uri' => 'http://127.0.0.1',
            'timeout' => 10
        ]);

        $total = 3000; // 每秒N次

        $this->line("开始压测: {$total} req/s\n", 'info');
        $start = microtime(true);

        // 创建 N 个协程
        for ($i = 0; $i < $total; $i++) {
            Coroutine::create(function () use ($client, $i) {
                try {
                    $client->get('/api/v1/test', [
                        'json' => [
                            'username' => 'username',
                            'password' => '123456',
                        ],
                    ]);
                } catch (\Throwable $e) {
                    $this->error($e->getMessage());
                }
            });
        }

        $use = microtime(true) - $start;

        $this->line("完成，耗时：{$use} 秒\n", 'info');
    }
}

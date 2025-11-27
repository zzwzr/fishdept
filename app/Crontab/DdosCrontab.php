<?php

declare(strict_types=1);

namespace App\Crontab;

use Hyperf\Crontab\Annotation\Crontab;
use Hyperf\Logger\LoggerFactory;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

#[Crontab(name: "ddos test", rule: "* * * * *", callback: "execute", enable: false, memo: "每2秒执行ddos任务")]
class DdosCrontab
{
    protected ContainerInterface $container;
    
    protected LoggerInterface $logger;

    public function __construct(ContainerInterface $container, LoggerFactory $loggerFactory)
    {
        $this->container = $container;
        $this->logger = $loggerFactory->get('crontab', 'default');
    }

    /**
     * 任务执行方法
     */
    public function execute(): void
    {
        $startTime = microtime(true);
        
        try {
            $this->logger->info('SSD任务开始执行', [
                'time' => date('Y-m-d H:i:s'),
                'memory' => memory_get_usage(true) / 1024 / 1024 . 'MB'
            ]);

            $returnCode = null;
            $result = shell_exec('php bin/hyperf.php ddos 2>&1');

            $executionTime = round((microtime(true) - $startTime) * 1000, 2);
            
            $this->logger->info('SSD任务执行完成', [
                'result' => $result,
                'execution_time' => $executionTime . 'ms',
                'return_code' => $returnCode,
                'memory_usage' => memory_get_usage(true) / 1024 / 1024 . 'MB'
            ]);

        } catch (\Throwable $e) {
            $this->logger->error('SSD任务执行失败', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    public function beforeExecute(): void
    {
        $this->logger->debug('SSD任务准备执行');
    }

    public function afterExecute(): void
    {
        $this->logger->debug('SSD任务执行结束');
    }
}
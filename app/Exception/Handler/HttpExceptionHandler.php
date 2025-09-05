<?php

namespace App\Exception\Handler;

use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\ExceptionHandler\Formatter\FormatterInterface;
use Hyperf\HttpMessage\Exception\HttpException;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Swow\Psr7\Message\ResponsePlusInterface;
use Throwable;

class HttpExceptionHandler extends ExceptionHandler
{
    public function __construct(protected StdoutLoggerInterface $logger, protected FormatterInterface $formatter)
    {
    }

    /**
     * Handle the exception, and return the specified result.
     * @param HttpException $throwable
     */
    public function handle(Throwable $throwable, ResponsePlusInterface $response)
    {
        $this->logger->debug($this->formatter->format($throwable));

        $this->stopPropagation();

        $data = json_encode(['code' => $throwable->getStatusCode(), 'message' => $throwable->getMessage()], JSON_UNESCAPED_UNICODE);
        return $response->setStatus($throwable->getStatusCode())->setBody(new SwooleStream($data));
    }

    /**
     * Determine if the current exception handler should handle the exception.
     *
     * @return bool If return true, then this exception handler will handle the exception,
     *              If return false, then delegate to next handler
     */
    public function isValid(Throwable $throwable): bool
    {
        return $throwable instanceof HttpException;
    }
}

<?php

namespace App\Exception\Handler;

use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Psr\Http\Message\ResponseInterface;
use Throwable;
use Hyperf\Validation\ValidationException;
use Fig\Http\Message\StatusCodeInterface;

class ValidationExceptionHandler extends ExceptionHandler
{
    public function handle(Throwable $throwable, ResponseInterface $response)
    {
        $this->stopPropagation();

        $body = $throwable->validator->errors()->first();

        return $response->withStatus(StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY)
                        ->withHeader('Content-type', 'application/json; charset=utf-8')
                        ->withHeader('Access-Control-Allow-Origin', '*')
                        ->withBody(new SwooleStream(json_encode(['code' => $throwable->status, 'message' => $body])));
    }

    /**
     * 判断该异常处理器是否要对该异常进行处理
     */
    public function isValid(Throwable $throwable): bool
    {
        return $throwable instanceof ValidationException;
    }
}

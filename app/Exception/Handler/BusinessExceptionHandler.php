<?php

namespace App\Exception\Handler;

use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Psr\Http\Message\ResponseInterface;
use App\Exception\BusinessException;
use Hyperf\Context\Context;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

class BusinessExceptionHandler extends ExceptionHandler
{
    public function handle(Throwable $throwable, ResponseInterface $response)
    {
        $this->stopPropagation();
        $request = Context::get(ServerRequestInterface::class);

        $data = json_encode(['code' => $throwable->getCode(), 'message' => $throwable->getMessage()], JSON_UNESCAPED_UNICODE);

        return $response->withStatus($throwable->getCode())
                        ->withHeader('Content-type', 'application/json; charset=utf-8')
                        ->withHeader('Access-Control-Allow-Origin', $request->getHeaderLine('origin'))
                        ->withHeader('Access-Control-Allow-Credentials', 'true')
                        ->withHeader('Access-Control-Allow-Headers', 'Keep-Alive, User-Agent, Cache-Control, Content-Type, Authorization')
                        ->withBody(new SwooleStream($data));
    }

    public function isValid(Throwable $throwable): bool
    {
        return $throwable instanceof BusinessException;
    }
}

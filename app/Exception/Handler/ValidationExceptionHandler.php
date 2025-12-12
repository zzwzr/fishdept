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
        // $request = Context::get(ServerRequestInterface::class);

        $data = json_encode(['code' => $throwable->getCode(), 'msg' => $throwable->validator->errors()->first(), 'data' => []], JSON_UNESCAPED_UNICODE);
        return $response->withStatus(StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY)
                        ->withHeader('Content-type', 'application/json; charset=utf-8')
                        ->withHeader('Access-Control-Allow-Origin', '*')
                        // ->withHeader('Access-Control-Allow-Origin', $request->getHeaderLine('origin'))
                        // ->withHeader('Access-Control-Allow-Credentials', 'true')
                        // ->withHeader('Access-Control-Allow-Headers', 'Keep-Alive, User-Agent, Cache-Control, Content-Type, Authorization')
                        ->withBody(new SwooleStream($data));
    }

    /**
     * 判断该异常处理器是否要对该异常进行处理
     */
    public function isValid(Throwable $throwable): bool
    {
        return $throwable instanceof ValidationException;
    }
}

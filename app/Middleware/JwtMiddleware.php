<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Tools\JwtTool;
use Hyperf\Context\Context;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Hyperf\HttpServer\Response;
use HyperfExtension\Jwt\Exceptions\JwtException;

class JwtMiddleware implements MiddlewareInterface
{
    public function __construct(
        protected JwtTool $jwtTool,
        protected ContainerInterface $container,
        protected Response $response,
    ) {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            $payload = $this->jwtTool->validateToken($request);
            if (!$payload) {
                return $this->response->json(['code' => 401, 'message' => 'Token 已失效'])->withStatus(401);
            }
            Context::set('user', $payload->toArray());

        } catch (JwtException $e) {
            return $this->response->json(['code' => 401, 'message' => '令牌无效'])->withStatus(401);
        }
        return $handler->handle($request);
    }
}
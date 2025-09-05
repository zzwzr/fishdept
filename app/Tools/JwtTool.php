<?php

declare(strict_types=1);

namespace App\Tools;

use App\Exception\BusinessException;
use HyperfExtension\Jwt\Contracts\JwtFactoryInterface;
use HyperfExtension\Jwt\Contracts\ManagerInterface;
use Psr\Http\Message\ServerRequestInterface;
use App\Model\User;
use HyperfExtension\Jwt\Payload;

class JwtTool
{
    /**
     * @var \HyperfExt\Jwt\Contracts\ManagerInterface
     */
    protected $manager;

    /**
     * @var \HyperfExt\Jwt\Jwt
     */
    protected $jwt;


    public function __construct(
        ManagerInterface $manager,
        JwtFactoryInterface $jwtFactory,
    ) {
        $this->manager = $manager;
        $this->jwt = $jwtFactory->make();
    }

    /**
     * 生成JWT Token并返回
     * @param User $user 用户对象
     * @return string 生成的Token
     */
    public function generateToken(User $user): string
    {
        $token = $this->jwt->fromUser($user);
        return $token;
    }

    /**
     * 从请求中解析并验证JWT Token
     * @param ServerRequestInterface $request 当前请求
     * @return Payload
     * @throws BusinessException
     */
    public function validateToken(): Payload
    {
        return $this->jwt->parseToken()->getPayload();
    }

    /**
     * 刷新Token并返回新的Token。
     *
     * @param bool $forceForever 是否强制设置永久有效
     * @return string 刷新后的Token
     */
    public function refreshToken(bool $forceForever = false): string
    {
        return $this->jwt->refresh($forceForever);
    }
}

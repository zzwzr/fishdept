<?php

declare(strict_types=1);

namespace App\Controller;

use App\Constants\ErrorCode;
use App\Model\User;
use App\Request\Login\RegisterRequest;
use Hyperf\Di\Annotation\Inject;
use Psr\EventDispatcher\EventDispatcherInterface;
use App\Event\UserRegistered;
use App\Exception\BusinessException;
use App\Request\Login\LoginRequest;
use App\Resource\Common\BaseResource;
use App\Resource\Login\LoginResource;
use App\Tools\JwtTool;

class LoginController
{
    #[Inject]
    private EventDispatcherInterface $eventDispatcher;

    /**
     * @var JwtTool
     */
    protected $jwtTool;

    public function __construct(JwtTool $jwtTool)
    {
        $this->jwtTool = $jwtTool;
    }

    public function register(RegisterRequest $request)
    {
        $input = $request->validated();

        $input['password'] = password_hash($input['password'], PASSWORD_BCRYPT);
        $user = User::create($input);

        // $this->eventDispatcher->dispatch(new UserRegistered($user)); // 注册完成后事件

        return new BaseResource();
    }

    public function login(LoginRequest $request)
    {
        $input = $request->validated();

        $user = User::where('mobile', $input['mobile'])->first();

        if (!$user) {
            throw new BusinessException(401, '用户不存在！');
        }

        if (!password_verify($input['password'], $user->password)) {
            throw new BusinessException(401, '用户或密码不正确！');
        }

        $token = $this->jwtTool->generateToken($user);

        $user->token = $token;

        return new LoginResource($user);
    }
}
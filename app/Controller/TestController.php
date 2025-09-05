<?php

declare(strict_types=1);

namespace App\Controller;

use App\Constants\ErrorCode;
use App\Exception\BusinessException;
use App\Exception\SystemException;
use App\JsonRpcInterface\SeckillServiceInterface;
use App\Logger\UserLogger;
use App\Model\User;
use App\Request\Login\RegisterRequest;
use App\Resource\Common\BaseResource;
use App\Resource\Login\RegisterResource;
use App\Resource\User\IndexCollection;
use App\Resource\User\IndexResource;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
// use Casbin\Enforcer;
use Donjan\Casbin\Enforcer;
use Hyperf\Context\ApplicationContext;
use Hyperf\Di\Annotation\Inject;

class TestController extends AbstractController
{
    /**
     * 服务调用
     * @param RequestInterface $request
     * @return void
     */
    public function test(RequestInterface $request)
    {
        try {
            $data = [
                'user_id'   => 123456,
                'user_ip'   => '192.168.1.100',
                'goods_id'  => 1001,
                'sku'       => 100101,
                'quantity'  => 1,
                'time'      => time()
            ];
            $secret = config('AMQP_MESSAGE_SECRET');
            $data['sign'] = generateSign($data, $secret);

            $seckill = $this->container->get(SeckillServiceInterface::class);
            $res = $seckill->push($data);
            if (0 != $res['code']) {
                throw new BusinessException(ErrorCode::SIGN_ERROR);
            }
            return new BaseResource($res['data']);
        } catch (\Throwable $th) {
            throw new BusinessException(10000, $th->getMessage());
        }
    }

    // public function test(RequestInterface $request)
    // {
    //     $uuid = $request->input('uuid');
    //     if ($uuid) {
    //         $redis = $this->container->get(\Hyperf\Redis\Redis::class);
    //         if ($redis->setnx($uuid, 'vv')) {
    //             echo "设置成功！\n";
    //         } else {
    //             echo "键已存在！\n";
    //         }
    //     }
    //     echo "缺少键！\n";
    //     return new BaseResource();
    // }

    // public function test(RegisterRequest $request)
    // {
    //     try {
    //         $list = User::paginate();
    //         return (new IndexResource($list))->toResponse();
    //         // 分页返回
    //         return IndexResource::collection($list)->toResponse();
    //     } catch (\Throwable $th) {
    //         // UserLogger::error($th->getMessage());
    //         throw new BusinessException(ErrorCode::SERVER_ERROR);
    //     }
    // }

    // public function test(RequestInterface $request)
    // {
    //     // 添加权限
    //     Enforcer::addPermissionForUser('4', '/user', 'read');
    //     return new BaseResource(['a1' => 'aaa', 'a2' => 222, 'a3' => 333]);
    // }
}

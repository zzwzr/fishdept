<?php

declare(strict_types=1);

namespace App\Controller;

use App\Request\User\CreateRequest;
use App\Request\User\DeleteRequest;
use App\Request\User\UpdateRequest;
use App\Resource\Common\BaseResource;
use App\Resource\User\IndexResource;
use App\Service\UserService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;

class UserController extends AbstractController
{
    #[Inject]
    private UserService $userService;

    public function create(CreateRequest $request)
    {
        $validated = $request->validated();

        $this->userService->createUser($validated);

        return new BaseResource();
    }

    public function delete(DeleteRequest $request)
    {
        $validated = $request->validated();

        $this->userService->deleteUser($validated['id']);

        return new BaseResource();
    }

    public function update(UpdateRequest $request)
    {
        $validated = $request->validated();

        $this->userService->updateUser($validated);

        return new BaseResource();
    }

    /**
     * 返回所有数据，无分页
     * @param RequestInterface $request
     * @return BaseResource
     */
    public function index(RequestInterface $request)
    {
        $filters = $request->all();

        $list = $this->userService->getUser($filters);

        return new BaseResource($list);
    }

    /**
     * 分页返回
     * @param RequestInterface $request
     * @return 
     */
    public function list(RequestInterface $request)
    {
        $filters = $request->all();

        $list = $this->userService->getPaginatedUser($filters);

        return IndexResource::collection($list)->additional(withAdditional());
    }
}
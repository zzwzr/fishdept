<?php

declare(strict_types=1);

namespace App\Controller;

use App\Request\Room\CreateRequest;
use App\Resource\Common\BaseResource;
use App\Service\RoomService;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;

class RoomController
{
    public function __construct(
        protected RoomService $roomService
    ) {}

    public function create(CreateRequest $request)
    {
        $validated = $request->validated();

        $result = $this->roomService->createRoom($validated['name'], $validated['browser_id']);

        return new BaseResource(['number' => $result]);
    }

    public function index(RequestInterface $request)
    {
        $search = $request->input('search');

        $result = $this->roomService->listRooms($search);

        return new BaseResource($result);
    }
}

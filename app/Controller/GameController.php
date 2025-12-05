<?php

declare(strict_types=1);

namespace App\Controller;

use App\Request\Game\MoveRequest;
use App\Request\Game\StartRequest;
use App\Resource\Common\BaseResource;
use App\Service\GameService;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;

class GameController
{
    public function __construct(
        protected GameService $gameService
    ) {}

    public function start(StartRequest $request)
    {
        $validated = $request->validated();

        $this->gameService->start($validated['room_id']);

        return new BaseResource();
    }

    public function move(MoveRequest $request)
    {
        $validated = $request->validated();

        $this->gameService->move($validated);

        return new BaseResource();
    }
}
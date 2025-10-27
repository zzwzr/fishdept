<?php

declare(strict_types=1);

namespace App\Controller;

use App\Resource\Common\BaseResource;
use App\Service\OnlineUserService;

class OnlineUserController
{
    public function __construct(
        protected OnlineUserService $onlineUserService
    ) {}

    public function getCount()
    {
        $count = $this->onlineUserService->getCount();
        return new BaseResource(['count' => $count]);
    }
}

<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\User;
use Hyperf\Contract\LengthAwarePaginatorInterface;

interface UserRepositoryInterface
{
    public function create(array $data): User;

    public function findByUserMobile(string|int $mobile): bool;

    public function delete(string|int $id): int;

    public function update(array $data): int;

    public function paginate(array $params = []): LengthAwarePaginatorInterface;
}

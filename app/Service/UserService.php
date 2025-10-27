<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\BusinessException;
use App\Model\User;
use App\Repository\UserRepository;
use Hyperf\Collection\Collection;
use Hyperf\Context\Context;
use Hyperf\Contract\LengthAwarePaginatorInterface;
use Hyperf\Di\Annotation\Inject;

class UserService
{
    #[Inject]
    private UserRepository $userRepository;

    public function createUser(array $userData): User
    {
        if ($this->userRepository->findByUserMobile($userData['mobile'])) {
            throw new BusinessException(409, '手机号已存在');
        }

        return $this->userRepository->create($userData);
    }

    public function getPaginatedUser(array $filters = []): LengthAwarePaginatorInterface
    {
        return $this->userRepository->paginate($filters);
    }

    public function getUser(array $filters = []): Collection
    {
        return $this->userRepository->all($filters);
    }

    public function updateUser(array $data): int
    {
        return $this->userRepository->update($data);
    }

    public function deleteUser(string|int $id): int
    {
        return $this->userRepository->delete($id);
    }

    public function findOne($where): ?User
    {
        return $this->userRepository->findOne($where);
    }

    public function findOrCreateByBrowserId(string $browserId): User
    {
        return $this->userRepository->findOrCreateByBrowserId($browserId);
    }
}
<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\User;
use Hyperf\Collection\Collection;
use Hyperf\Contract\LengthAwarePaginatorInterface;

class UserRepository implements UserRepositoryInterface
{
    public function __construct(private User $model) {}

    public function create(array $data): User
    {
        return $this->model->create($data);
    }

    public function findByUserMobile(string|int $mobile): bool
    {
        return $this->model->where('mobile', $mobile)->first() ? true : false;
    }

    public function delete(string|int $id): int
    {
        return $this->model->where('id', $id)->delete();
    }

    public function update(array $data): int
    {
        $id = $data['id'];
        unset($data['id']);
        return $this->model->where('id', $id)->update($data);
    }

    public function paginate(array $params = []): LengthAwarePaginatorInterface
    {
        $model = $this->model->query();

        if (!empty($params['name'])) {
            $model->where('name', 'like', '%' . $params['name'] . '%');
        }

        $model->orderBy('id', 'desc');

        return $model->paginate();
    }

    public function all(array $params = []): Collection
    {
        $model = $this->model->query();

        if (!empty($params['name'])) {
            $model->where('name', 'like', '%' . $params['name'] . '%');
        }

        $model->orderBy('id', 'desc');

        return $model->get();
    }

    public function findOne(array $where = []): ?User
    {
        return $this->model->when($where, fn($q) => $q->where($where))->orderByDesc('id')->first();
    }

    public function findOrCreateByBrowserId(string $browserId): User
    {
        $user = $this->model->where('browser_id', $browserId)->first();
        if (!$user) {
            $user = $this->model->newInstance([
                'browser_id'    => $browserId,
                'name'          => 'guest_' . bin2hex(random_bytes(4)),
            ]);
            $user->save();
        }

        return $user;
    }
}

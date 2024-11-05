<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class UsersService
{
    /**
     * Create a new class instance.
     */
    private Model $model;
    public function __construct(User $user)
    {
        $this->model = $user;
    }

    public function model(): User
    {
        return $this->model;
    }

    public function select2(): Builder
    {
        $users =  $this->model()
            ->select([
                'id',
                DB::raw('name as text'),
            ])
            ->limit(10);

        return $users;
    }

    public function paginate($filter, $page): LengthAwarePaginator
    {
        $search = $filter['q'] ?? '';
        $users = $this->model
            ->when($search, function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%");
            })
            ->paginate($page);
        return $users;
    }

    public function create(array $data)
    {
        try {
            $user = $this->model->create($data);
            return $user;
        } catch (\Throwable $th) {
            throw new \ErrorException($th->getMessage());
        }
    }

    public function show($id): User
    {
        return $this->model->find($id);
    }

    public function update(array $data, User $user): bool
    {
        try {
            $user = $user->update($data);
            return $user;
        } catch (\Throwable $th) {
            throw new \ErrorException($th->getMessage());
        }
    }

    public function destroy(User $user): bool
    {
        try {
            return $user->delete();
        } catch (\Throwable $th) {
            throw new \ErrorException($th->getMessage());
        }
    }

    public function hasActiveToken(User $user)
    {
        return $user->tokens()
            ->where('revoked', false)
            ->exists();
    }

    public function revokeToken(User $user)
    {
        return $user->tokens()
            ->where('revoked', false)
            ->update(['revoked' => true]);
    }

    public function createToken(User $user, string $appName)
    {
        return $user->createToken($appName);
    }
}

<?php

namespace App\Services;

use App\Models\Wishlist;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class WishlistService
{
    /**
     * Create a new class instance.
     */
    private Model $model;
    public function __construct(Wishlist $wishlist)
    {
        $this->model = $wishlist;
    }

    public function model(): Wishlist
    {
        return $this->model;
    }

    public function paginate($filter, $page): LengthAwarePaginator
    {
        $search = $filter['q'] ?? '';
        $userId = auth()->user()->id;

        $wishlists = $this->model
            ->where('buyer_id', $userId)
            ->when($search, function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate($page);
        return $wishlists;
    }

    public function findByProductBuyer($product_id)
    {
        $userId = auth()->user()->id;

        $wishlist = $this->model
            ->where('buyer_id', $userId)
            ->where('product_id', $product_id)
            ->first();
        return $wishlist;
    }

    public function create(array $data)
    {
        try {
            $data['buyer_id'] = $data['buyer_id'] ?? auth()->user()->id;
            $wishlist = $this->model->create($data);
            return $wishlist;
        } catch (\Throwable $th) {
            throw new \ErrorException($th->getMessage());
        }
    }

    public function show($id): Wishlist
    {
        return $this->model->find($id);
    }

    public function update(array $data, Wishlist $wishlist): bool
    {
        try {
            $wishlist = $wishlist->update($data);
            return $wishlist;
        } catch (\Throwable $th) {
            throw new \ErrorException($th->getMessage());
        }
    }

    public function destroy(Wishlist $wishlist): bool
    {
        try {
            return $wishlist->delete();
        } catch (\Throwable $th) {
            throw new \ErrorException($th->getMessage());
        }
    }
}

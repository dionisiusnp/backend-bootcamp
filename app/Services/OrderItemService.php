<?php

namespace App\Services;

use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class OrderItemService
{
    /**
     * Create a new class instance.
     */
    private Model $model;
    public function __construct(OrderItem $orderItem)
    {
        $this->model = $orderItem;
    }

    public function model(): OrderItem
    {
        return $this->model;
    }

    public function paginate($order_id): Collection
    {
        $orderItems = $this->model
            ->where('order_id', $order_id)
            ->orderBy('created_at', 'asc')
            ->get();
        return $orderItems;
    }

    public function create(array $data)
    {
        try {
            $orderItem = $this->model->create($data);
            return $orderItem;
        } catch (\Throwable $th) {
            throw new \ErrorException($th->getMessage());
        }
    }

    public function show($id): OrderItem
    {
        return $this->model->find($id);
    }

    public function update(array $data, OrderItem $orderItem): bool
    {
        try {
            $orderItem = $orderItem->update($data);
            return $orderItem;
        } catch (\Throwable $th) {
            throw new \ErrorException($th->getMessage());
        }
    }

    public function destroy(OrderItem $orderItem): bool
    {
        try {
            return $orderItem->delete();
        } catch (\Throwable $th) {
            throw new \ErrorException($th->getMessage());
        }
    }
}

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
    public $orderService;
    public function __construct(OrderItem $orderItem, OrderService $orderService)
    {
        $this->model = $orderItem;
        $this->orderService = $orderService;
    }

    public function model(): OrderItem
    {
        return $this->model;
    }

    public function cart($buyer_id): Collection
    {
        $orderItems = $this->model
        ->with('order', 'product')
        ->whereHas('order', function ($query) use ($buyer_id) {
            $query->where('buyer_id', $buyer_id)
                  ->whereNull('payment_method_id');
        })
        ->orderBy('created_at', 'asc')
        ->get();
        
    return $orderItems;
    }

    public function paginate($order_id): Collection
    {
        $orderItems = $this->model
            ->with('order')
            ->where('order_id', $order_id)
            ->orderBy('created_at', 'asc')
            ->get();
        return $orderItems;
    }

    public function create(array $data)
    {
        try {
            $order = $this->orderService->model()
            ->where('buyer_id', $data['buyer_id'])
            ->where('payment_method_id', null)
            ->first();

            if (!$order) {
                $orderData = [
                    'buyer_id' => $data['buyer_id'],
                ];
                $order = $this->orderService->create($orderData);
            }

            $existingItem = $this->model()
                ->where('order_id', $order->id)
                ->where('product_id', $data['product_id'])
                ->first();

            if ($existingItem) {
                $existingItem->quantity += $data['quantity'];
                $existingItem->total_sub = ($existingItem->quantity * $existingItem->price) + $existingItem->shipping_cost;
                $existingItem->save();
                $orderItem = $existingItem;
            } else {
                $data['order_id'] = $order->id;
                $data['total_sub'] = ($data['quantity'] * $data['price']) + $data['shipping_cost'];
                $orderItem = $this->model()->create($data);
            }
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

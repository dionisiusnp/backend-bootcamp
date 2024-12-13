<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderService
{
    /**
     * Create a new class instance.
     */
    private Model $model;

    public function __construct(Order $order)
    {
        $this->model = $order;
    }

    public function model(): Order
    {
        return $this->model;
    }

    public function select2(): Builder
    {
        $orders =  $this->model()
            ->select([
                'id',
                DB::raw('name as text'),
            ])
            ->limit(10);

        return $orders;
    }

    public function paginate($filter, $page): LengthAwarePaginator
    {
        $userId = $filter['buyer_id'] ?? null;

        $orders = $this->model
            ->with(['userable', 'media'])
            ->when($userId, function ($q) use ($userId) {
                $q->where('buyer_id', $userId);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($page);

        $orders->getCollection()->transform(function ($order) {
            $order->media_urls = $order->getMedia('order_images')->map(function ($media) {
                return $media->getFullUrl();
            });
            return $order;
        });

        return $orders;
    }

    public function lastOrder($filter,)
    {
        $buyerId = $filter['buyer_id'] ?? null;
        $order = $this->model
            ->with('orderItems')
            ->withSum('orderItems as total_payment', 'total_sub')
            ->where('payment_method_id', null)
            ->when($buyerId, function ($q) use ($buyerId) {
                $q->where('buyer_id', $buyerId);
            })
            ->get()->first();
        return $order;
    }

    public function create(array $data)
    {
        try {
            $data['buyer_id'] = $data['buyer_id'];
            $order = $this->model->create($data);
            return $order;
        } catch (\Throwable $th) {
            throw new \ErrorException($th->getMessage());
        }
    }

    public function show($id): Order
    {
        return $this->model->find($id);
    }

    public function update(array $data, Order $order): bool
    {
        try {
            $order = $order->update($data);
            return $order;
        } catch (\Throwable $th) {
            throw new \ErrorException($th->getMessage());
        }
    }

    public function destroy(Order $order): bool
    {
        try {
            return $order->delete();
        } catch (\Throwable $th) {
            throw new \ErrorException($th->getMessage());
        }
    }
}

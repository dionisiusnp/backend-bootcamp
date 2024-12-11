<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

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
        $search = $filter['q'] ?? '';
        $userId = $filter['user_id'] ?? null;
        $paymentMethodId = $filter['payment_method_id'] ?? null;
        $isPayment = $filter['is_payment'] ?? false;
        $isAccept = $filter['is_accept'] ?? false;
        $isDelivery = $filter['is_delivery'] ?? false;

        $orders = $this->model
            ->with(['userable', 'media'])
            ->when($userId, function ($q) use ($userId) {
                $q->where('buyer_id', $userId);
            })
            ->when($paymentMethodId, function ($q) use ($paymentMethodId) {
                $q->where('payment_method_id', $paymentMethodId);
            })
            ->when($isPayment !== null, function ($q) use ($isPayment) {
                $q->where('is_payment', $isPayment);
            })
            ->when($isAccept !== null, function ($q) use ($isAccept) {
                $q->where('is_accept', $isAccept);
            })
            ->when($isDelivery !== null, function ($q) use ($isDelivery) {
                $q->where('is_delivery', $isDelivery);
            })
            ->when($search, function ($q) use ($search) {
                $q->whereHas('userable', function ($query) use ($search) {
                    $query->where('name', 'LIKE', "%{$search}%");
                });
                $q->orWhere('id', 'LIKE', "%{$search}%");
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

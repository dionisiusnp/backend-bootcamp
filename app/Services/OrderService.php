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

    public $orderItemService;
    public function __construct(Order $order, OrderItemService $orderItemService)
    {
        $this->model = $order;
        $this->orderItemService = $orderItemService;
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
        $userId = $filter['user_id'] ?? auth()->user()->id;

        $orders = $this->model
            ->where('buyer_id', $userId)
            ->when($search, function ($q) use ($search) {
                $q->where('id', 'LIKE', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate($page);
        return $orders;
    }

    public function paginateSeller($filter, $page): LengthAwarePaginator
    {
        $search = $filter['q'] ?? '';

        $orders = $this->model
            ->when($search, function ($q) use ($search) {
                $q->where('id', 'LIKE', "%{$search}%");
            })
            ->orderBy('created_at', 'asc')
            ->paginate($page);
        return $orders;
    }

    public function create(array $data)
    {
        try {
            $data['buyer_id'] = $data['buyer_id'] ?? auth()->user()->id;
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

<?php

namespace App\Services;

use App\Models\PaymentMethod;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class PaymentMethodService
{
    /**
     * Create a new class instance.
     */
    private Model $model;
    public function __construct(PaymentMethod $paymentMethod)
    {
        $this->model = $paymentMethod;
    }

    public function model(): PaymentMethod
    {
        return $this->model;
    }

    public function select2(): Builder
    {
        $paymentMethods =  $this->model()
            ->select([
                'id',
                DB::raw('name as text'),
            ])
            ->limit(10);

        return $paymentMethods;
    }

    public function paginate($filter, $page): LengthAwarePaginator
    {
        $search = $filter['q'] ?? '';
        $paymentMethods = $this->model
            ->when($search, function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%");
            })
            ->paginate($page);
        return $paymentMethods;
    }

    public function create(array $data)
    {
        try {
            $paymentMethod = $this->model->create($data);
            return $paymentMethod;
        } catch (\Throwable $th) {
            throw new \ErrorException($th->getMessage());
        }
    }

    public function show($id): PaymentMethod
    {
        return $this->model->find($id);
    }

    public function update(array $data, PaymentMethod $paymentMethod): bool
    {
        try {
            $paymentMethod = $paymentMethod->update($data);
            return $paymentMethod;
        } catch (\Throwable $th) {
            throw new \ErrorException($th->getMessage());
        }
    }

    public function destroy(PaymentMethod $paymentMethod): bool
    {
        try {
            return $paymentMethod->delete();
        } catch (\Throwable $th) {
            throw new \ErrorException($th->getMessage());
        }
    }
}

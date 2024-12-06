<?php

namespace App\Services;

use App\Models\ProductCategory;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ProductCategoryService
{
    /**
     * Create a new class instance.
     */
    private Model $model;
    public function __construct(ProductCategory $productCategory)
    {
        $this->model = $productCategory;
    }

    public function model(): ProductCategory
    {
        return $this->model;
    }

    public function select2(): Builder
    {
        $productCategories =  $this->model()
            ->select([
                'id',
                DB::raw('name as text'),
            ])
            ->limit(10);

        return $productCategories;
    }

    public function paginate($filter, $page): LengthAwarePaginator
    {
        $search = $filter['q'] ?? '';
        $productCategories = $this->model
            ->when($search, function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%");
            })
            ->orderBy('name', 'asc')
            ->paginate($page);
        return $productCategories;
    }

    public function create(array $data)
    {
        try {
            $productCategory = $this->model->create($data);
            return $productCategory;
        } catch (\Throwable $th) {
            throw new \ErrorException($th->getMessage());
        }
    }

    public function show($id): ProductCategory
    {
        return $this->model->find($id);
    }

    public function update(array $data, ProductCategory $productCategory): bool
    {
        try {
            $productCategory = $productCategory->update($data);
            return $productCategory;
        } catch (\Throwable $th) {
            throw new \ErrorException($th->getMessage());
        }
    }

    public function destroy(ProductCategory $productCategory): bool
    {
        try {
            if ($productCategory->products()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kategori tidak dapat dihapus karena sedang digunakan dalam produk.',
                ], 400);
            }
            return $productCategory->delete();
        } catch (\Throwable $th) {
            throw new \ErrorException($th->getMessage());
        }
    }
}

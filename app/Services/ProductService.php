<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductService
{
    /**
     * Create a new class instance.
     */
    private Model $model;
    public function __construct(Product $product)
    {
        $this->model = $product;
    }

    public function model(): Product
    {
        return $this->model;
    }

    public function paginate($filter, $page): LengthAwarePaginator
    {
        $search = $filter['q'] ?? '';
        $products = $this->model
            ->with(['productCategory', 'media'])
            ->when($search, function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%");
            })
            ->orderBy('name', 'asc')
            ->paginate($page);

            $products->getCollection()->transform(function ($product) {
                $product->media_urls = $product->getMedia('product_images')->map(function ($media) {
                    return $media->getFullUrl();
                });
                return $product;
            });
        return $products;
    }

    public function create(array $data)
    {
        try {
            $product = $this->model->create($data);
            return $product;
        } catch (\Throwable $th) {
            throw new \ErrorException($th->getMessage());
        }
    }

    public function show($id): Product
    {
        return $this->model->find($id);
    }

    public function update(array $data, Product $product): bool
    {
        try {
            $product = $product->update($data);
            return $product;
        } catch (\Throwable $th) {
            throw new \ErrorException($th->getMessage());
        }
    }

    public function destroy(Product $product): bool
    {
        try {
            return $product->delete();
        } catch (\Throwable $th) {
            throw new \ErrorException($th->getMessage());
        }
    }
}

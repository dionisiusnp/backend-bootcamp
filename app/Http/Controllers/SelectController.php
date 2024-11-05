<?php

namespace App\Http\Controllers;

use App\Services\PaymentMethodService;
use App\Services\ProductCategoryService;
use App\Services\UsersService;
use Illuminate\Http\Request;

class SelectController extends Controller
{
    public $productCategoryService, $paymentMethodService, $usersService;

    public function __construct(ProductCategoryService $productCategoryService, PaymentMethodService $paymentMethodService, UsersService $usersService)
    {
        $this->productCategoryService = $productCategoryService;
        $this->paymentMethodService = $paymentMethodService;
        $this->usersService = $usersService;
    }

    public function categories(Request $request)
    {
            $term = trim($request->q);
            $filters = $request->filter;
            $query = $this->productCategoryService->select2($filters);
            if (empty($term)) {
                $productCategories = $query->get();
            } else {
                $productCategories = $query->where('name', 'like', '%' . $term . '%')
                ->get();
            }
            return response()->json($productCategories);
    }

    public function payments(Request $request)
    {
            $term = trim($request->q);
            $filters = $request->filter;
            $query = $this->paymentMethodService->select2($filters);
            if (empty($term)) {
                $paymentMethods = $query->get();
            } else {
                $paymentMethods = $query->where('name', 'like', '%' . $term . '%')
                ->get();
            }
            return response()->json($paymentMethods);
    }

    public function customers(Request $request)
    {
        if ($request->wantsJson()) {
            $term = trim($request->q);
            $filters = $request->filter;
            $query = $this->usersService->select2($filters)->where('is_seller', false);
            if (empty($term)) {
                $users = $query->get();
            } else {
                $users = $query->where('name', 'like', '%' . $term . '%')
                    ->get();
            }
            return response()->json($users);
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filter = $request->only(['q', 'user_id']);
        $orders = $this->orderService->paginate($filter, 10);
        return response()->json($orders->withQueryString());
    }

    public function indexSeller(Request $request)
    {
        $filter = $request->only(['q']);
        $orders = $this->orderService->paginateSeller($filter, 10);
        return response()->json($orders->withQueryString());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $this->orderService->create($request->all());
            return response()->json([
                'success' => true,
                'message' => 'Berhasil',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal',
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        try {
            $order = $this->orderService->show($order);
            return response()->json([
                'success' => true,
                'message' => 'Berhasil',
                'data' => $order,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal',
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        try {
            $this->orderService->update($request->all(), $order);
            return response()->json([
                'success' => true,
                'message' => 'Berhasil',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal',
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        try {
            $this->orderService->destroy($order);
            return response()->json([
                'success' => true,
                'message' => 'Berhasil',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal',
            ], 500);
        }
    }
}
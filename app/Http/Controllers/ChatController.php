<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Services\ChatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    public $chatService;

    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filter = $request->only(['user_id']);
        $chats = $this->chatService->paginate($filter);
        return response()->json($chats);
    }

    public function indexSeller(Request $request)
    {
        $filter = $request->only(['q']);
        $chats = $this->chatService->paginateSeller($filter, 10);
        return response()->json($chats->withQueryString());
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
            $request->merge([
                'is_seller_reply' => filter_var($request->input('is_seller_reply'), FILTER_VALIDATE_BOOLEAN),
            ]);
            $chat = $this->chatService->create($request->all());
            if ($request->hasFile('image')) {
                $chat->addMedia($request->file('image'))->toMediaCollection('chat_images');
            }
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
    public function show(Chat $chat)
    {
        try {
            $chat = $this->chatService->show($chat);
            return response()->json([
                'success' => true,
                'message' => 'Berhasil',
                'data' => $chat,
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
    public function edit(Chat $chat)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Chat $chat)
    {
        try {
            $this->chatService->update($request->all(), $chat);
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
    public function destroy(Chat $chat)
    {
        try {
            $this->chatService->destroy($chat);
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

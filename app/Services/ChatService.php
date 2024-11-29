<?php

namespace App\Services;

use App\Models\Chat;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ChatService
{
    /**
     * Create a new class instance.
     */
    private Model $model;
    public function __construct(Chat $chat)
    {
        $this->model = $chat;
    }

    public function model(): Chat
    {
        return $this->model;
    }

    public function paginate($filter, $page): LengthAwarePaginator
    {
        $search = $filter['q'] ?? '';
        $userId = $filter['user_id'] ?? auth()->user()->id;

        $chats = $this->model
            ->where('user_id', $userId)
            ->when($search, function ($q) use ($search) {
                $q->where('message', 'LIKE', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate($page);
        return $chats;
    }

    public function paginateSeller($filter, $page): LengthAwarePaginator
    {
        $search = $filter['q'] ?? '';

        $chats = $this->model
            ->select(
                'user_id',
                DB::raw('MAX(created_at) as latest_message_time'),
                DB::raw('SUBSTRING_INDEX(GROUP_CONCAT(message ORDER BY created_at DESC), ",", 1) as latest_message'),
                )
            ->with('userable')
            ->where('is_seller_reply', false)
            ->when($search, function ($q) use ($search) {
                $q->where('chats.message', 'LIKE', "%{$search}%")
                  ->orWhereHas('userable', function ($query) use ($search) {
                      $query->where('name', 'LIKE', "%{$search}%");
                  });
            })
            ->groupBy('user_id')
            ->orderBy('latest_message_time', 'desc');
        return $chats->paginate($page);
    }

    public function create(array $data)
    {
        try {
            if (auth()->user()->is_seller === true) {
                $data['is_seller_reply'] = true;
            } else {
                $data['is_seller_reply'] = false;
            }
            $chats = $this->model->create($data);
            return $chats;
        } catch (\Throwable $th) {
            throw new \ErrorException($th->getMessage());
        }
    }

    public function show($id): Chat
    {
        return $this->model->find($id);
    }

    public function update(array $data, Chat $chat): bool
    {
        try {
            $chat = $chat->update($data);
            return $chat;
        } catch (\Throwable $th) {
            throw new \ErrorException($th->getMessage());
        }
    }

    public function destroy(Chat $chat): bool
    {
        try {
            return $chat->delete();
        } catch (\Throwable $th) {
            throw new \ErrorException($th->getMessage());
        }
    }
}

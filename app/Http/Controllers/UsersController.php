<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\UsersService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class UsersController extends Controller
{
    public $usersService;

    public function __construct(UsersService $usersService)
    {
        $this->usersService = $usersService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filter = $request->only(['q']);
        $users = $this->usersService->paginate($filter, 10);
        return response()->json($users->withQueryString());
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
            $this->usersService->create($request->all());
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
    public function show(User $user)
    {
        try {
            $user = $this->usersService->show($user);
            return response()->json([
                'success' => true,
                'message' => 'Berhasil',
                'data' => $user,
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
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        try {
            $this->usersService->update($request->all(), $user);
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
    public function destroy(User $user)
    {
        try {
            $this->usersService->destroy($user);
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

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $auth = Auth::user();
        $user = $this->usersService->model()->find($auth->id);
        $this->usersService->revokeToken($user);
        $token = $this->usersService->createToken($user,'Flutter Apps');

        return response()->json([
            'success' => true,
            'token' => $token->accessToken,
            'data' => $user,
        ], 200);
    }
}

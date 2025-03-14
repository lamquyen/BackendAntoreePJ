<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function getUser(){
        
        $users = User::paginate(20); // Lấy 10 user mỗi trang

    return response()->json($users);
    }


    public function showUser(Request $request)
    {
        $user = $request->user(); // Lấy user từ token
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json($user);
    }
    public function updateUser(Request $request)
    {
        $user = $request->user(); // Lấy user từ token

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Kiểm tra dữ liệu đầu vào
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => ['sometimes', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'sometimes|string|min:6'
        ]);

        // Cập nhật thông tin user
        $user->update([
            'name' => $request->name ?? $user->name,
            'email' => $request->email ?? $user->email,
            'password' => $request->password ? Hash::make($request->password) : $user->password
        ]);

        return response()->json([
            'message' => 'User updated successfully!',
            'user' => $user
        ]);
    }


    public function updateUserByAdmin(Request $request, $id)
{
    $admin = $request->user();
    if (!$admin || $admin->role !== 'admin') {
        return response()->json(['message' => 'Bạn không có quyền!'], 403);
    }

    $user = User::find($id);
    if (!$user) {
        return response()->json(['message' => 'User không tồn tại!'], 404);
    }

    Log::info('Trước khi cập nhật:', $user->toArray());
    Log::info('Dữ liệu nhận được từ frontend:', $request->all());

    $request->validate([
        'name' => 'sometimes|string|max:255',
        'email' => ['sometimes', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
        'role' => 'sometimes|in:admin,user',
    ]);

    $user->update([
        'name' => $request->name ?? $user->name,
        'email' => $request->email ?? $user->email,
        'role' => $request->role ?? $user->role,
    ]);

    Log::info('Sau khi cập nhật:', $user->toArray()); 

    return response()->json([
        'message' => 'User updated successfully!',
        'user' => $user
    ]);
}



    
    public function CreateUser(Request $request)
    {
        // Validate dữ liệu đầu vào
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'role' => ['nullable', Rule::in(['user', 'admin'])], // Chỉ chấp nhận 'user' hoặc 'admin'
        ]);

        // Tạo user mới
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Mã hóa mật khẩu
            'role' => $request->role ?? 'user', 
        ]);

        return response()->json([
            'message' => 'User created successfully!',
            'user' => $user
        ], 201);
    }
    public function destroyUser($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->delete();
        return response()->json(['message' => 'User deleted successfully']);
    }
}


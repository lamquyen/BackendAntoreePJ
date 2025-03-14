<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\RateLimiter\RequestRateLimiterInterface;

class AuthController extends Controller
{
    // Phương thức đăng nhập
    public function login(Request $request)
    {
        // Xác thực dữ liệu đầu vào
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        // Kiểm tra xem người dùng có tồn tại không và mật khẩu có đúng không
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Tạo token cho người dùng
        $token = $user->createToken('MyApp')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
        ]);
    }
    public function logout(Request $request)
    {
        if (!$request->user()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    
        // Xóa tất cả token của user
        $request->user()->tokens()->delete();
    
        return response()->json(['message' => 'Logged out successfully']);
    }
}

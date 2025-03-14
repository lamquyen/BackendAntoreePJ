<?php
namespace App\Http\Middleware;
use Illuminate\Support\Facades\Auth;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\User;
class UserOrAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        $id = $request->route('id'); // Lấy ID từ route

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Chỉ cho phép admin hoặc chính chủ
        if ($user->role !== 'admin' && $user->id != $id) {
            return response()->json(['message' => 'Access denied.'], 403);
        }

        return $next($request);
    }
}

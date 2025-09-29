<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Helpers\API\{CommonHelper};

class CheckPasswordExpiration
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */

    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        $error_status_code = CommonHelper::PASSWORD_EXP_ERROR_STATUS;

        if($user && $user->password_changed_at == null){
            return response()->json([
                'success' => false,
                'message' => 'Your password has expired. Please change it.'
            ], $error_status_code);
        }elseif ($user && $user->password_changed_at) {

            $passwordChangedAt = Carbon::parse($user->password_changed_at);
            if ($passwordChangedAt->diffInDays(Carbon::now()) >= 30) {

                return response()->json([
                    'success' => false,
                    'message' => 'Your password has expired. Please change it.'
                ], $error_status_code);

            }
        }

        return $next($request);
    }
}

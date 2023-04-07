<?php

namespace App\Http\Middleware;

use App\Utilities\Data;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class JwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return Data::makeResponseForm(
                false, null, 401, "Token is invalid",
            );
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return Data::makeResponseForm(
                false, null, 401, "Token is expired",
            );
        } catch (Exception $e) {
            return Data::makeResponseForm(
                false, null, 401, "Token not found",
            );
        }
        return $next($request);
    }
}

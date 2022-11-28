<?php

namespace App\Http\Middleware;

use App\Http\Helper;
use App\Models\User;
use Carbon\Carbon;
use Closure;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Auth;
use Firebase\JWT\Key;

class JWTMiddle
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        $token = $request->header("Authorization");

        //$key = env("JWT_KEY");
        $key = config("settings.JWT_SECRET");

        if(!$token || empty($token))
            return response(Helper::jsonError("Authorization missing!"), 401, []);

        JWT::$leeway = 60 * 60 * 24;
//        $decoded = JWT::decode($token, $key, array('HS256'));
        $decoded = JWT::decode($token, new Key($key, 'HS256'));
        $expire = Carbon::createFromTimestamp($decoded->expirationDate / 1000);
        $now = Carbon::now();

        if($now->gt($expire))
            return response("Authorization token has expired!", 401);

        $user = User::where('id','=',$decoded->id)->first();

        if(!$user)
            return response("User not found", 404, []);

        Auth::login($user);

        return $next($request);
    }
}

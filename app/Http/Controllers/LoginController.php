<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Resources\UserLoginResource;
use App\Models\User;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use function config;
use function response;

class LoginController extends Controller
{
    public function register(UserRegisterRequest $request)
    {
        $userNameExists = User::where("name", $request->name)->first();

        if ($userNameExists) {
            return response()->json(['error' => 'Name already exists.', 'code' => 404], 404);
        }

        $userMailExists = User::where("email", $request->email)->first();

        if ($userMailExists) {
            return response()->json(['error' => 'Email already exists.', 'code' => 404], 404);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $jwtUser = [
            'id' => $user->id,
            'email' => $user->email,
            'expirationDate' => time() * 1000 + (30 * 24 * 60 * 60 * 1000),
        ];

        $key = Config::get("settings.JWT_SECRET");
        $jwt = JWT::encode($jwtUser, $key, 'HS256');

        Auth::login($user);

        return response()->json([
            'jwt' => $jwt,
            'user' => UserLoginResource::make($user),
        ]);
    }

    public function login(UserLoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['error' => 'Wrong email.', 'code' => 404], 404);
        }

        if (!Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'Wrong password.', 'code' => 404], 404);
        }

        $jwtUser = [
            'id' => $user->id,
            'email' => $user->email,
            'expirationDate' => time() * 1000 + (30 * 24 * 60 * 60 * 1000),
        ];

        $key = config('settings.JWT_SECRET');
        $jwt = \Firebase\JWT\JWT::encode($jwtUser, $key, 'HS256');

        Auth::login($user);

        return response()->json([
            'jwt' => $jwt,
            'user' => UserLoginResource::make($user),
        ]);
    }
}

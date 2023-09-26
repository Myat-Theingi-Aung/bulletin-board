<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * User login
     *
     * @param LoginRequest $request
     * @return \Illuminate\Http\Response $token $remember_token $user
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        $remember = $request->remember;

        if (Auth::attempt($credentials, $remember)) {
            $user = $user = User::where('email', $request->email)->first();

            $token = $user->createToken('auth_token')->plainTextToken;
            $cookie = cookie('token', $token, 60 * 24); // 1 day

            return response()->json(['success' => 'Login Successfully!','user' => new UserResource($user), 'token' => $token, 'remember' => $user->remember_token])->withCookie($cookie);
        }

        return response()->json(['error' => 'Email or password is incorrect!'], 422);
    }

    /**
     * User logout
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        $cookie = cookie()->forget('token');

        return response()->json(['message' => 'Logged out successfully!'])->withCookie($cookie);
    }
}

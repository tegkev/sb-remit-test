<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->input('email'))->first();

        if (!$user || !Hash::check($request->input('password'), $user->password)) {
            return response()->json(['message' => __('Bad credentials')], 422);
        }

        return [
            'user' => (new UserResource($user))->toArray($request),
            'token' => $user->createToken('token')->plainTextToken,
        ];
    }

    public function register(RegisterRequest $request)
    {
        $user = User::create(array_merge($request->all(), ['password' => Hash::make($request->input('password'))]));

        return [
            'user' => (new UserResource($user))->toArray($request),
            'token' => $user->createToken('token')->plainTextToken,
        ];
    }
}

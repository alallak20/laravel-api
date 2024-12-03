<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Requests\Api\LoginUserRequest;
use App\Models\User;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use ApiResponses;
    public function login(LoginUserRequest $request)
    {
        $validatedData = $request->validated();
//        return $this->ok('My first API, JK :)');
//        $request->validate($request->all());
//
        if (!Auth::attempt($request->only(['email', 'password']))) {
            return $this->error('Invalid credentials', 401);
        }

        $user = User::firstWhere('email', $request->email);
//
        return $this->ok('Authenticated', [
            'token' => $user->createToken('API Token For ' . $user->email,
            ['*'],
            now()->addMonth())->plainTextToken,
        ]);
    }

    public function logout (Request $request)
    {
//        $request->user()->tokens()->where('id', 13)->delete();
//        $request->user()->tokens()->delete();
        $request->user()->currentAccessToken()->delete();

        return $this->ok('');
    }
}

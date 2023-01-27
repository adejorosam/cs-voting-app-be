<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required',
            'password' => 'required'
        ]);

        // Check email
        $user = User::where('email', $validated['email'])->first();

        // Check password
        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return response([
                'status' => 'Error',
                'message' => 'Bad Credentials',
            ], 401);
        }

        $token = $user->createToken('app_token')->plainTextToken;

        return response(['user' => $user, 'token' => $token], 201);
    }
}

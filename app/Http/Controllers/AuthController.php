<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register a new user.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $validated = Validator::make($request->all(),[
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => [
                'required',
                'string',
                'confirmed',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/'
            ],
            'role' => 'required|string|in:admin,user', // Modifier les rôles selon vos besoins
        ]);
        

        /*$user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return response()->json([
            'message' => 'User registered successfully'
        ], 201);*/
        $user = User::create($request->all());
        $token = $user->createToken('authToken', ['*']);
        return response()->json([
            'token' => $token->plainTextToken,
            'message' => 'Vous vous êtes bien enregistré .'
        ]);
    }

    /**
     * Log in a user.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 500);
        }

        $user = Auth::user();
        $token = $user->createToken('Personal Access Token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'role' => $user->role
            ]
        ]);
    }

    /**
     * Log out a user.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }

    /**
     * Get the authenticated user.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    /**
     * Update the authenticated user's profile.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        $request->validate([
            'username' => 'sometimes|string|max:255|unique:users,username,' . $request->user()->id,
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $request->user()->id,
            'password' => 'sometimes|string|confirmed|min:8',
            'role' => 'sometimes|string|in:admin,user', // Modifier les rôles selon vos besoins
        ]);

        $user = $request->user();

        if ($request->has('username')) {
            $user->username = $request->username;
        }

        if ($request->has('email')) {
            $user->email = $request->email;
        }

        if ($request->has('password')) {
            $user->password = Hash::make($request->password);
        }

        if ($request->has('role')) {
            $user->role = $request->role;
        }

        $user->save();

        return response()->json([
            'message' => 'Profile updated successfully'
        ]);
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\User; // Corrected alias
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException; // Corrected namespace

class AuthController extends Controller
{
    /**
     * Register a new user.
     *
     * Validates input, creates a new user with the specified role,
     * and returns the user data along with an API token.
     *
     * @param Request $request The incoming HTTP request.
     * @return \Illuminate\Http\JsonResponse JSON response with user data and token.
     */
    public function register(Request $request)
    {
        // Validate incoming request data
        $fields = $request->validate([
            'name'     => 'required|string',
            'email'    => 'required|string|email|unique:users,email', // Email must be unique in the users table
            'password' => 'required|string|confirmed', // Password must be confirmed (e.g., password_confirmation field)
            'role'     => 'required|in:creator,player', // Role must be either 'creator' or 'player'
        ]);

        // Create the user
        $user = User::create([
            'name'     => $fields['name'],
            'email'    => $fields['email'],
            'password' => bcrypt($fields['password']), // Hash the password for security
            'role'     => $fields['role'], // Assign the role from validated input
        ]);

        // Create an API token for the new user
        $token = $user->createToken('api-token')->plainTextToken; // 'api-token' is a descriptive name for the token type

        // Return user data and token, with HTTP 201 Created status
        return response()->json([
            'data' => [
                'user'  => $user,
                'token' => $token,
            ]
        ], 201);
    }

    /**
     * Log in an existing user.
     *
     * Validates credentials, and if successful, returns the user data
     * along with an API token.
     *
     * @param Request $request The incoming HTTP request.
     * @return \Illuminate\Http\JsonResponse JSON response with user data and token or error.
     */
    public function login(Request $request)
    {
        // Validate incoming request data
        $fields = $request->validate([
            'email'    => 'required|string|email',
            'password' => 'required|string',
        ]);

        // Attempt to find the user by email
        $user = User::where('email', $fields['email'])->first();

        // Check if user exists and password is correct
        if (!$user || !Hash::check($fields['password'], $user->password)) {
            // If authentication fails, throw a validation exception
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials.'], // Generic message for security
            ]);
        }

        // Create an API token for the authenticated user
        $token = $user->createToken('api-token')->plainTextToken; // 'api-token' is a descriptive name

        // Return user data and token
        return response()->json([
            'data' => [
                'user'  => $user,
                'token' => $token,
            ]
        ]);
    }

    /**
     * Get the authenticated user's details.
     *
     * @param Request $request The incoming HTTP request (implicitly uses authenticated user).
     * @return \Illuminate\Http\JsonResponse JSON response with the authenticated user's data.
     */
    public function me(Request $request)
    {
        // Return the currently authenticated user's data
        return response()->json([
            'data' => $request->user()
        ]);
    }

    /**
     * Log out the authenticated user.
     *
     * Revokes the current API token used for authentication.
     *
     * @param Request $request The incoming HTTP request (implicitly uses authenticated user).
     * @return \Illuminate\Http\JsonResponse JSON response with a success message.
     */
    public function logout(Request $request)
    {
        // Revoke the token that was used to authenticate the current request
        $request->user()->currentAccessToken()->delete();

        // Return a success message
        return response()->json(['message' => 'Logged out successfully']);
    }
}

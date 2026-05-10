<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Rules\NotDisposable;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'username' => ['nullable', 'string', 'max:255', 'unique:users,username'],
            'email' => ['required', 'string', 'email', 'unique:users,email', new NotDisposable],
            'phone' => ['nullable', 'string', 'max:255', 'unique:users,phone'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $user = User::create([
            'name' => $validated['name'] ?? Str::before($validated['email'], '@'),
            'username' => $validated['username'] ?? null,
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => 0,
            'status' => 1,
        ]);

        // Send verification email link
        $user->sendEmailVerificationNotification();

        // Generate manual link for testing (Postman only)
        $verificationUrl = url('/api/user/verify-email/' . $user->id . '/' . sha1($user->email));

        return response()->json([
            'status' => true,
            'message' => 'Account created! Please verify your email.',
            // 'verification_link' => $verificationUrl, // এটি টেস্টিং এর জন্য দেওয়া হলো
            'user' => $user
        ]);
    }

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email', 'exists:users,email'],
            'password' => ['required', 'string'],
        ], [
            'email.exists' => 'This email is not registered.'
        ]);

        $token = Auth::guard('api')->attempt($credentials);

        if (!$token) {
            throw ValidationException::withMessages([
                'password' => ['Incorrect password.'],
            ]);
        }

        $user = Auth::guard('api')->user();

        // Check if email is verified
        if (!$user->hasVerifiedEmail()) {
            Auth::guard('api')->logout();
            return response()->json([
                'status' => false,
                'message' => 'Please verify your email address before logging in.'
            ], 403);
        }

        if ((int) $user->status !== 1) {
            Auth::guard('api')->logout();
            return response()->json([
                'status' => false,
                'message' => 'Account is inactive.'
            ], 403);
        }

        return $this->respondWithToken($token, $user, 'Login successful.');
    }

    public function verifyEmail(Request $request, $id, $hash): JsonResponse
    {
        $user = User::findOrFail($id);

        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return response()->json(['status' => false, 'message' => 'Invalid verification link.'], 400);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(['status' => true, 'message' => 'Email already verified.']);
        }

        if ($user->markEmailAsVerified()) {
            event(new \Illuminate\Auth\Events\Verified($user));
        }

        return response()->json(['status' => true, 'message' => 'Email verified successfully! You can now login.']);
    }

    public function resendVerification(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email|exists:users,email']);
        $user = User::where('email', $request->email)->first();

        if ($user->hasVerifiedEmail()) {
            return response()->json(['status' => false, 'message' => 'Email already verified.']);
        }

        $user->sendEmailVerificationNotification();

        return response()->json(['status' => true, 'message' => 'Verification link sent to your email.']);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'status' => true,
            'data' => $request->user(),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        Auth::guard('api')->logout();

        return response()->json([
            'status' => true,
            'message' => 'Logged out successfully.'
        ]);
    }

    public function refresh(): JsonResponse
    {
        $token = Auth::guard('api')->refresh();
        $user = Auth::guard('api')->user();

        return $this->respondWithToken($token, $user, 'Token refreshed successfully.');
    }

    protected function respondWithToken(string $token, ?User $user = null, string $message = ''): JsonResponse
    {
        $payload = [
            'status' => true,
            'message' => $message,
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::guard('api')->factory()->getTTL() * 60,
        ];

        if ($user) {
            $payload['user'] = $user;
        }

        return response()->json($payload);
    }
}

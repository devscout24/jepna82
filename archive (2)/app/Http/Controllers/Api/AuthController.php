<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\ForgetPasswordMail;
use App\Models\PackageAndSubscription;
use App\Models\User;
use App\Traits\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;


class AuthController extends Controller
{
    use ApiResponse;
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'username' => ['nullable', 'string', 'max:255', 'unique:users,username'],
            'email' => [
                'required',
                'string',
                'email',
                'unique:users,email',
                'indisposable',
                function ($attribute, $value, $fail) {
                    $blockedDomains = ['bezill.com', 'yopmail.com', 'tempmail.com', 'mailinator.com'];
                    $domain = substr(strrchr($value, "@"), 1);
                    if (in_array(strtolower($domain), $blockedDomains)) {
                        $fail('Disposable email addresses are not allowed.');
                    }
                }
            ],
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


        // Build a custom verification URL pointing to our API endpoint
        $verificationUrl = route('api.verification.verify', ['id' => $user->id, 'hash' => sha1($user->email)]);

        // Send custom verification email with our API link
        \Illuminate\Support\Facades\Mail::raw(
            "Please verify your email by clicking the link below:\n\n" . $verificationUrl . "\n\nThis link will verify your account and redirect you to the login page.",
            function ($message) use ($user) {
                $message->to($user->email)->subject('Verify Your Email Address');
            }
        );

        return response()->json([
            'status' => true,
            'message' => 'Account created! Please verify your email.',
            'verification_link' => $verificationUrl, // টেস্টিং এর জন্য
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

    public function verifyEmail(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);

        // Frontend URL - login page e redirect korbe
        $frontendLoginUrl = env('FRONTEND_URL', 'https://your-frontend.com') . '/login';

        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return redirect($frontendLoginUrl . '?verified=false&message=Invalid+verification+link');
        }

        if ($user->hasVerifiedEmail()) {
            return redirect($frontendLoginUrl . '?verified=true&message=Email+already+verified');
        }

        if ($user->markEmailAsVerified()) {
            event(new \Illuminate\Auth\Events\Verified($user));
        }

        return redirect($frontendLoginUrl . '?verified=true&message=Email+verified+successfully');
    }

    public function resendVerification(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email|exists:users,email']);
        $user = User::where('email', $request->email)->first();

        if ($user->hasVerifiedEmail()) {
            return response()->json(['status' => false, 'message' => 'Email already verified.']);
        }

        // Build a custom verification URL pointing to our API endpoint
        $verificationUrl = route('api.verification.verify', ['id' => $user->id, 'hash' => sha1($user->email)]);

        // Send custom verification email with our API link
        \Illuminate\Support\Facades\Mail::raw(
            "Please verify your email by clicking the link below:\n\n" . $verificationUrl . "\n\nThis link will verify your account and redirect you to the login page.",
            function ($message) use ($user) {
                $message->to($user->email)->subject('Verify Your Email Address');
            }
        );

        return response()->json(['status' => true, 'message' => 'Verification link sent to your email.']);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'status' => true,
            'data' => $request->user(),
        ]);
    }

    public function logout()
    {
        try {
            // Get token from request
            $token = JWTAuth::getToken();

            if (!$token) {
                return $this->error([], 'Token not provided', 401);
            }

            // Invalidate token
            JWTAuth::invalidate($token);

            return $this->success([], 'Successfully logged out', 200);
        } catch (JWTException $e) {
            return $this->error([], 'Failed to logout. ' . $e->getMessage(), 500);
        }
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
            'expires_in' => Auth::guard('api')->factory()->getTTL() ? Auth::guard('api')->factory()->getTTL() * 60 : null,
        ];

        if ($user) {
            $payload['user'] = $user;
        }

        return response()->json($payload);
    }



    public function forgetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors(), 'Validation Error', 422);
        }

        try {
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not found',
                    'data' => (object)[]
                ], 404);
            }

            $otp = rand(100000, 999999);
            $user->otp = $otp;
            $user->otp_expires_at = now()->addMinutes(5);
            $user->save();

            $user->makeHidden(['password', 'created_at', 'updated_at']);
            $user->makeVisible(['otp', 'otp_expires_at']);


            Mail::to($user->email)->send(new ForgetPasswordMail($user));




            return $this->success($user, 'OTP sent successfully');
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage());
        }
    }

    public function checkOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'otp' => 'required',
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        try {
            $user = User::where('email', $request->email)
                ->where('otp', $request->otp)
                ->first();

            if (!$user) {
                return $this->error([], 'Invalid OTP', 401);
            }

            if ($user->otp_expires_at < now()) {
                return $this->error([], 'OTP expired', 401);
            }

            $user->email_verified_at = now();
            $user->otp_verified_at = now();
            $user->password_reset_token = Str::random(60);
            $user->password_reset_token_expires_at = now()->addMinutes(30);
            $user->save();

            return $this->success($user, 'OTP verified successfully');
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage());
        }
    }


    public function resetPassword(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'password' => 'required|min:6|confirmed',
            'email' => 'required|email',
            'password_reset_token' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors());
        }

        try {
            $user = User::where('email', $request->email)
                ->whereNotNull('otp_verified_at')
                ->where('password_reset_token', $request->password_reset_token)
                ->first();



            if (!$user) {
                return $this->error([], 'Please try again', 401);
            }

            if ($user->password_reset_token_expires_at < now()) {
                return $this->error([], 'Token expired', 401);
            }

            $user->password = Hash::make($request->password);
            $user->save();
            $logout = $this->logout();
            // dd($logout);
            return $this->success($user, 'Password reset successfully');
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage());
        }
    }

    public function resendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        try {
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return $this->notFound([], 'User not found');
            }

            $otp = rand(100000, 999999);
            $user->otp = $otp;
            $user->otp_expires_at = now()->addMinutes(3);
            $user->save();

            Mail::to($user->email)->send(new ForgetPasswordMail($user));

            return $this->success($user, 'OTP resent successfully');
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage());
        }
    }


    public function changePassword(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'new_password' => 'required|min:6|confirmed',
            'current_password' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Validation Error', 422);
        }

        try {
            $user = Auth::guard('api')->user();
            if (Hash::check($request->current_password, $user->password)) {
                $user->password = Hash::make($request->new_password);
                $user->save();
                return $this->success($user, 'Password changed successfully');
            }
            return $this->error([], ' Current password does not match', 401);
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage());
        }
    }




    public function deleteAccount(Request $request)
    {
        try {
            $user = Auth::guard('api')->user();
            $user->email = '';
            $user->save();
            $user->delete();
            return $this->success([], 'Account deleted successfully');
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage());
        }
    }



    public function userProfileImage()
    {
        try {
            $user = Auth::guard('api')->user();
            $profile_image = asset($user->profile_image);
            $data = [
                'profile_image' => $profile_image,
                'name' => $user->name,
                'email' => $user->email
            ];
            return $this->success($profile_image, 'Profile image fetched successfully');
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage());
        }
    }


    public function switchAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        try {

            $user = auth('api')->user();
            if (!$user) {
                return $this->unauthorized([], 'Invalid token or user not found');
            }

            $targetRole = $request->role;

            // Check if the user has the target role, otherwise assign it
            if (!$user->hasRole($targetRole)) {
                $user->assignRole($targetRole);
            }

            // Automatically assign other roles if missing
            if (!$user->hasRole('service_provider')) {
                $user->assignRole('service_provider');
            }
            if (!$user->hasRole('user')) {
                $user->assignRole('user');
            }

            // ✅ Update last_login_role first
            $user->last_login_role = $targetRole;
            $user->save();

            // Generate token with updated role
            $token = auth('api')->claims(['role' => $targetRole])->login($user);

            // Prepare account info
            if ($targetRole === 'service_provider') {
                $account = $user->stripesetup()->first();

                if (!$account) {
                    return $this->success([
                        'token' => $token,
                        'status' => 'pending',
                        'account_info' => $user,
                    ], 'Please first set up your stripe account');
                }
            }

            $accountInfo = $user; // now this will have updated last_login_role

            return $this->success([
                'token' => $token,
                'account_info' => $accountInfo,
            ], 'Account switched successfully');
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage());
        }
    }


    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',


            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        try {
            $user = Auth::guard('api')->user();

            if ($request->hasFile('profile_image')) {
                $image = $request->file('profile_image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $path = "uploads/profile_images/";
                $image->move(public_path($path), $imageName);
                $user->profile_image = $path . $imageName;
            }

            if ($request->name) {
                $user->name = $request->name;
            }


            $user->save();

            return $this->success($user, 'Profile updated successfully');
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage());
        }
    }

    public function profile(Request $request)
    {
        try {
            $user = Auth::guard('api')->user();
            $data = [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'profile_image' => asset($user->profile_image),
            ];
            return $this->success($data, 'Profile fetched successfully');
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage());
        }
    }
}

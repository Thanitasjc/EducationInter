<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:50'],
            'locale' => ['nullable', 'in:th,en'],
        ]);

        $user = User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'locale' => $data['locale'] ?? 'th',
            'password' => $data['password'],
            'last_login_at' => now(),
        ]);

        $user->assignRole('student');

        Student::query()->create([
            'user_id' => $user->id,
            'preferred_locale' => $data['locale'] ?? 'th',
        ]);

        $token = $user->createToken('student')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user->load('roles'),
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::query()->where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        if (! $user->is_active) {
            throw ValidationException::withMessages([
                'email' => ['This account is inactive.'],
            ]);
        }

        $user->forceFill(['last_login_at' => now()])->save();
        $token = $user->createToken('student')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user->load('roles'),
        ]);
    }

    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        try {
            Password::broker()->sendResetLink($request->only('email'));
        } catch (\Throwable $e) {
            Log::warning('Password reset email failed', [
                'email' => $request->input('email'),
                'error' => $e->getMessage(),
            ]);
        }

        // Always generic — do not reveal whether the email exists.
        return response()->json([
            'message' => 'If that email exists, a reset link has been sent.',
        ]);
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'token' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $status = Password::broker()->reset(
            [
                'email' => $data['email'],
                'password' => $data['password'],
                'password_confirmation' => $request->input('password_confirmation'),
                'token' => $data['token'],
            ],
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => $password,
                    'remember_token' => Str::random(60),
                    'last_login_at' => now(),
                    'is_active' => true,
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        }

        $user = User::query()->where('email', $data['email'])->firstOrFail();
        if (! $user->hasRole('student')) {
            $user->assignRole('student');
        }
        Student::query()->firstOrCreate(
            ['user_id' => $user->id],
            ['preferred_locale' => $user->locale ?? 'th']
        );

        $token = $user->createToken('student')->plainTextToken;

        return response()->json([
            'message' => 'Password updated',
            'token' => $token,
            'user' => $user->load('roles'),
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'user' => $request->user()->load(['roles', 'student']),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json(['message' => 'Logged out']);
    }
}

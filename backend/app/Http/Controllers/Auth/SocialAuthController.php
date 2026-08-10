<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    /** @var array<int, string> */
    private array $providers = ['facebook', 'line'];

    public function redirect(string $provider): RedirectResponse
    {
        abort_unless(in_array($provider, $this->providers, true), 404);

        return Socialite::driver($provider)->stateless()->redirect();
    }

    public function callback(Request $request, string $provider): RedirectResponse|JsonResponse
    {
        abort_unless(in_array($provider, $this->providers, true), 404);

        $socialUser = Socialite::driver($provider)->stateless()->user();

        $user = User::query()
            ->where('provider', $provider)
            ->where('provider_id', $socialUser->getId())
            ->first();

        if (! $user && $socialUser->getEmail()) {
            $user = User::query()->where('email', $socialUser->getEmail())->first();
        }

        if (! $user) {
            $user = User::query()->create([
                'name' => $socialUser->getName() ?: ($socialUser->getNickname() ?: 'Student'),
                'email' => $socialUser->getEmail() ?: "{$provider}_{$socialUser->getId()}@users.local",
                'password' => Hash::make(Str::random(32)),
                'provider' => $provider,
                'provider_id' => $socialUser->getId(),
                'avatar_path' => $socialUser->getAvatar(),
                'last_login_at' => now(),
            ]);

            $user->assignRole('student');

            Student::query()->firstOrCreate(
                ['user_id' => $user->id],
                ['preferred_locale' => 'th']
            );
        } else {
            $user->forceFill([
                'provider' => $provider,
                'provider_id' => $socialUser->getId(),
                'avatar_path' => $socialUser->getAvatar() ?: $user->avatar_path,
                'last_login_at' => now(),
            ])->save();

            if (! $user->hasRole('student') && ! $user->hasAnyRole(['super_admin', 'admin', 'consultant'])) {
                $user->assignRole('student');
            }

            Student::query()->firstOrCreate(
                ['user_id' => $user->id],
                ['preferred_locale' => $user->locale ?? 'th']
            );
        }

        $token = $user->createToken('student')->plainTextToken;
        $frontend = rtrim(config('app.frontend_url', env('FRONTEND_URL', 'http://localhost:3000')), '/');

        return redirect()->away("{$frontend}/auth/callback?token={$token}");
    }
}

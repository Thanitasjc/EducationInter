<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\SocialAuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    /** @var array<int, string> */
    private array $providers = ['facebook'];

    public function redirect(string $provider): RedirectResponse
    {
        abort_unless(in_array($provider, $this->providers, true), 404);

        return Socialite::driver($provider)
            ->scopes(['openid', 'profile'])
            ->stateless()
            ->redirect();
    }

    public function callback(Request $request, string $provider, SocialAuthService $socialAuth): RedirectResponse|JsonResponse
    {
        abort_unless(in_array($provider, $this->providers, true), 404);

        $frontend = rtrim(config('app.frontend_url', env('FRONTEND_URL', 'http://localhost:3000')), '/');

        if ($request->filled('error')) {
            return redirect()->away("{$frontend}/th/login?error=social_login_failed");
        }

        try {
            $socialUser = Socialite::driver($provider)
                ->scopes(['openid', 'profile'])
                ->stateless()
                ->user();
        } catch (\Throwable $exception) {
            Log::warning('Social login callback failed.', [
                'provider' => $provider,
                'error' => $exception->getMessage(),
            ]);

            return redirect()->away("{$frontend}/th/login?error=social_login_failed");
        }

        $user = $socialAuth->resolveUser(
            provider: $provider,
            providerId: (string) $socialUser->getId(),
            name: $socialUser->getName() ?: $socialUser->getNickname(),
            email: $socialUser->getEmail(),
            avatar: $socialUser->getAvatar(),
        );

        $token = $user->createToken('student')->plainTextToken;

        return redirect()->away("{$frontend}/auth/callback?token={$token}");
    }
}

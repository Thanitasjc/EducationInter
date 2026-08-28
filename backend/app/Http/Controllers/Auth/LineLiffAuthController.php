<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\SocialAuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LineLiffAuthController extends Controller
{
    public function __invoke(Request $request, SocialAuthService $socialAuth): JsonResponse
    {
        $validated = $request->validate([
            'id_token' => ['required', 'string'],
        ]);

        $channelId = config('services.line.channel_id');

        if (! $channelId) {
            return response()->json(['message' => 'LINE login is not configured.'], 503);
        }

        try {
            $response = Http::asForm()
                ->timeout(10)
                ->post('https://api.line.me/oauth2/v2.1/verify', [
                    'id_token' => $validated['id_token'],
                    'client_id' => $channelId,
                ]);

            if (! $response->successful()) {
                Log::warning('LINE LIFF id_token verification failed.', [
                    'status' => $response->status(),
                    'body' => $response->json(),
                ]);

                return response()->json(['message' => 'Invalid LINE token.'], 401);
            }

            $profile = $response->json();
            $lineUserId = (string) ($profile['sub'] ?? '');

            if ($lineUserId === '') {
                return response()->json(['message' => 'Invalid LINE profile.'], 401);
            }

            $user = $socialAuth->resolveUser(
                provider: 'line',
                providerId: $lineUserId,
                name: $profile['name'] ?? null,
                email: $profile['email'] ?? null,
                avatar: $profile['picture'] ?? null,
            );

            $token = $user->createToken('student')->plainTextToken;

            return response()->json([
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
            ]);
        } catch (\Throwable $exception) {
            Log::warning('LINE LIFF login failed.', [
                'error' => $exception->getMessage(),
            ]);

            return response()->json(['message' => 'LINE login failed.'], 500);
        }
    }
}

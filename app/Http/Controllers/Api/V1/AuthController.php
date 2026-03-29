<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:255'],
        ]);

        /** @var User|null $user */
        $user = User::query()->where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => [__('auth.failed')],
            ]);
        }

        if ($user->hasRole('Orang Tua') && ! $user->anaks()->where('status', 'approved')->exists()) {
            return response()->json([
                'message' => 'Akun Anda belum aktif. Silakan tunggu persetujuan dari Admin Sekolah.',
            ], 403);
        }

        $device = $data['device_name'] ?? ($request->userAgent() ?: 'api');
        $token = $user->createToken($device)->plainTextToken;

        return response()->json([
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => $this->userPayload($user),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Berhasil keluar.']);
    }

    public function user(Request $request): JsonResponse
    {
        return response()->json($this->userPayload($request->user()));
    }

    /**
     * @return array<string, mixed>
     */
    private function userPayload(User $user): array
    {
        $user->loadMissing(['sekolah', 'kelas']);

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'sekolah_id' => $user->sekolah_id,
            'kelas_id' => $user->kelas_id,
            'roles' => $user->getRoleNames()->values()->all(),
            'sekolah' => $user->sekolah ? [
                'id' => $user->sekolah->id,
                'name' => $user->sekolah->name,
            ] : null,
        ];
    }
}

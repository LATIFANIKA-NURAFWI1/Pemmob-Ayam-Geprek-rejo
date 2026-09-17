<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

/**
 * AuthController — menangani login/logout via API token (Sanctum).
 *
 * Token digunakan sebagai Bearer token di header Authorization
 * untuk semua request selanjutnya.
 */
class AuthController extends Controller
{
    /**
     * POST /api/v1/login
     *
     * Melakukan autentikasi dan mengembalikan token Sanctum.
     * Mobile menyimpan token ini di secure storage (EncryptedSharedPreferences).
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => ['Email atau password salah.'],
            ]);
        }

        $user = Auth::user();

        // Cek apakah akun aktif (N9.1 — middleware analog untuk API)
        if (! $user->is_active) {
            Auth::logout();

            return response()->json([
                'success' => false,
                'message' => 'Akun Anda telah dinonaktifkan. Hubungi administrator.',
            ], 403);
        }

        // Hapus token lama agar tidak menumpuk (optional: bisa dipertahankan untuk multi-device)
        $user->tokens()->delete();

        // Buat token baru dengan abilities berdasarkan role
        $token = $user->createToken(
            name: "mobile-{$user->role}-{$user->id}",
            abilities: [$user->role]
        )->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => "Selamat datang, {$user->name}!",
            'data'    => [
                'token' => $token,
                'user'  => [
                    'id'        => $user->id,
                    'name'      => $user->name,
                    'email'     => $user->email,
                    'role'      => $user->role,
                    'is_active' => $user->is_active,
                ],
            ],
        ]);
    }

    /**
     * POST /api/v1/logout
     *
     * Menghapus token aktif (invalidate session mobile).
     */
    public function logout(Request $request): JsonResponse
    {
        // Hapus hanya token yang digunakan saat ini
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil logout.',
        ]);
    }
}

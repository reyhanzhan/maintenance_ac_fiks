<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ApiToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:100'],
        ]);

        $user = User::where('username', $credentials['username'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json(['message' => 'Username atau password salah.'], 422);
        }

        $plainToken = Str::random(80);

        ApiToken::create([
            'user_id' => $user->id,
            'name' => $credentials['device_name'] ?? 'flutter',
            'token_hash' => hash('sha256', $plainToken),
        ]);

        return response()->json([
            'token_type' => 'Bearer',
            'access_token' => $plainToken,
            'user' => $this->userPayload($user),
        ]);
    }

    public function me(Request $request)
    {
        return response()->json([
            'user' => $this->userPayload($request->user()),
        ]);
    }

    public function logout(Request $request)
    {
        $request->attributes->get('api_token')?->delete();

        return response()->json(['message' => 'Logout berhasil.']);
    }

    private function userPayload(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'username' => $user->username,
            'email' => $user->email,
            'role' => $user->role,
            'signature_url' => $user->signature_path ? asset('storage/' . $user->signature_path) : null,
        ];
    }
}

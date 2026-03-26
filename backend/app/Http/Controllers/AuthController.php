<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Usuários  de login em memória (sem banco de dados).
     * Fiz isso apenas, para não ficar uma tela inicial com os cadastros sem autenticação.
     */
    private array $users = [
        [
            'id'       => 1,
            'name'     => 'Administrador',
            'email'    => 'admin@sesab.ba.gov.br',
            'password' => 'admin123',
            'role'     => 'admin',
        ],
        [
            'id'       => 2,
            'name'     => 'Usuário Teste',
            'email'    => 'usuario@sesab.ba.gov.br',
            'password' => 'usuario123',
            'role'     => 'user',
        ],
    ];

    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $user = collect($this->users)->first(
            fn ($u) => $u['email'] === $request->email && $u['password'] === $request->password
        );

        if (! $user) {
            return response()->json(['message' => 'Credenciais inválidas.'], 401);
        }

        $token = Str::random(60);

        // Armazena token em memória (cache driver array — dura enquanto a aplicação estiver rodando)
        Cache::put("auth_token:{$token}", $user, now()->addHours(8));

        return response()->json([
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id'    => $user['id'],
                'name'  => $user['name'],
                'email' => $user['email'],
                'role'  => $user['role'],
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $token = $request->bearerToken();
        Cache::forget("auth_token:{$token}");

        return response()->json(['message' => 'Logout realizado com sucesso.']);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->attributes->get('auth_user');

        return response()->json([
            'id'    => $user['id'],
            'name'  => $user['name'],
            'email' => $user['email'],
            'role'  => $user['role'],
        ]);
    }
}

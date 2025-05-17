<?php

namespace App\Http\Controllers\Auth\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest; // Reutilizamos el mismo LoginRequest
use Illuminate\Http\JsonResponse; // Usaremos JsonResponse para las respuestas API
use Illuminate\Support\Facades\Auth; // Necesario para Auth::attempt
use Illuminate\Validation\ValidationException; // Para manejar errores de credenciales

class LoginController extends Controller
{
    /**
     * Maneja una solicitud de autenticación entrante a través de la API.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $request->authenticate();
        $user = $request->user();
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'message' => 'Login exitoso.',
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * Cierra la sesión del usuario desde la API (revoca el token).
     * Requiere autenticación con Sanctum.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Sesión cerrada exitosamente.']);
    }
}


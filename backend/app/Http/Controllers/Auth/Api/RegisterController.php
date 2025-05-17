<?php

namespace App\Http\Controllers\Auth\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;

class RegisterController extends Controller
{
    /**
     * Maneja una solicitud de registro entrante a través de la API.
     *
     * @param  \App\Http\Requests\Auth\RegisterRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        // El RegisterRequest ya validó los datos.
        // Crea el usuario en la base de datos.
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'birthdate' => $request->birthdate,
        ]);

        // Pendiente por configurar y enviar notificación de registro
        event(new Registered($user));

        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'message' => 'Usuario registrado exitosamente.',
            'user' => $user,
            'access_token' => $token, // Incluye el token si se generó
            'token_type' => 'Bearer', // Indica el tipo de token
        ], 201); // Código 201 Created
    }
}

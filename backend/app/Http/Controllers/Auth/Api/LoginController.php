<?php

namespace App\Http\Controllers\Auth\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;

/**
 * @group Autenticación
 * 
 * Endpoints para iniciar sesión (login) y cerrar sesión (logout), facilitando 
 * así el acceso seguro y la gestión de sesiones dentro de la aplicación. 
 */
class LoginController extends Controller
{
  /**
   * Autenticar usuario y obtener token API.
   *
   * Procesa las credenciales del usuario (email y contraseña)
   * y, si son correctas, retorna un token de acceso tipo Bearer.
   *
   * @bodyParam email string required El correo electrónico del usuario. Example: usuario@example.com
   * @bodyParam password string required La contraseña del usuario. Example: password123
   * @bodyParam remember bool Indica si se debe recordar la sesión (opcional). Example: true
   *
   * @response 200 {
   * "message": "Login exitoso.",
   * "access_token": "TU_TOKEN_AQUI",
   * "token_type": "Bearer"
   * }
   * @response 422 scenario="Credenciales Incorrectas o Usuario No Encontrado" {
   * "message": "Las credenciales proporcionadas son incorrectas.",
   * "errors": {
   * "email": [
   * "Las credenciales proporcionadas son incorrectas."
   * ]
   * }
   * }
   * @response 422 scenario="Demasiados Intentos de Login" {
   * "message": "Demasiados intentos de login. Por favor, inténtalo de nuevo en :seconds segundos.",
   * "errors": {
   * "email": [
   * "Demasiados intentos de login. Por favor, inténtalo de nuevo en 60 segundos."
   * ]
   * }
   * }
   * @response 422 scenario="Errores de Validación de Entrada" {
   * "message": "Los datos proporcionados no son válidos.",
   * "errors": {
   * "email": [
   * "El formato del correo electrónico no es válido."
   * ]
   * }
   * }
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
   * Cerrar sesión (revocar token API actual).
   *
   * Cierra la sesión del usuario autenticado revocando el token API
   * que se utilizó para hacer esta petición.
   *
   * @group Autenticación
   *
   * @authenticated
   *
   * @response 200 {
   * "message": "Sesión cerrada exitosamente."
   * }
   * @response 401 {
   * "message": "Unauthenticated."
   * }
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


<?php

namespace App\Http\Controllers\Auth\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;

/**
 * @group Autenticación
 * 
 * Endpoint responsable de manejar el proceso de registro de nuevos usuarios en la aplicación.
 * Contiene el endpoint necesario para que los nuevos usuarios puedan crear una cuenta (register). 
 */
class RegisterController extends Controller
{
  /**
   * Registrar un nuevo usuario.
   *
   * Crea una nueva cuenta de usuario con la información proporcionada.
   *
   * @bodyParam name string required El nombre completo del usuario. Example: Juan Pérez
   * @bodyParam email string required El correo electrónico del usuario. Debe ser único. Example: juan.perez@example.com
   * @bodyParam password string required La contraseña del usuario (mínimo 8 caracteres por defecto de Laravel Password). Example: s3cur3P@ssw0rd
   * @bodyParam password_confirmation string required Confirmación de la contraseña. Debe coincidir con 'password'. Example: s3cur3P@ssw0rd
   * @bodyParam birthdate date required La fecha de nacimiento del usuario. Debe ser una fecha válida y el usuario debe tener al menos 12 años. Example: 15/05/2001
   *
   * @response 201 {
   *   "message": "Usuario registrado exitosamente.",
   *   "user": {
   *     "id": 1,
   *     "name": "Juan Pérez",
   *     "email": "juan.perez@example.com",
   *     "created_at": "2023-10-27T10:00:00.000000Z",
   *     "updated_at": "2023-10-27T10:00:00.000000Z"
   *   }
   * }
   * @response 422 scenario="Errores de Validación" {
   *   "message": "Los datos proporcionados no son válidos.",
   *   "errors": {
   *     "email": [
   *       "El correo electrónico ya ha sido registrado."
   *     ],
   *     "password": [
   *       "La confirmación de contraseña no coincide."
   *     ],
   *     "birthdate": [
   *       "La fecha de nacimiento debe ser una fecha anterior o igual a 2012-10-27."
   *     ]
   *   }
   * }
   *
   * @param \App\Http\Requests\Auth\UpdateUserRequest $request
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

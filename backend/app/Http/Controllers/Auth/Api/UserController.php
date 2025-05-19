<?php

namespace App\Http\Controllers\Auth\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\UpdatePasswordRequest;
use App\Http\Requests\Auth\UpdateUserRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * @group Gestión de Usuario
 * 
 * Endpoints para gestionar las operaciones relacionadas con el perfil del usuario autenticado.
 * Este controlador proporciona endpoints para ver los detalles del usuario (show), 
 * actualizar su información (update), cambiar su contraseña (updatePassword) y eliminar su cuenta (destroy). 
 * Todos los métodos en este controlador están diseñados para operar sobre el usuario que realiza la solicitud.
 */
class UserController extends Controller
{
  /**
   * Muestra la información del usuario autenticado.
   * 
   * Retorna los datos del perfil del usuario que está actualmente autenticado.
   * 
   * @authenticated
   *
   * @response 200 {
   *   "id": 1,
   *   "name": "Usuario Autenticado",
   *   "email": "autenticado@example.com",
   *   "created_at": "2023-10-27T10:00:00.000000Z",
   *   "updated_at": "2023-10-27T10:00:00.000000Z"
   * }
   * @response 401 {
   *  "message": "Unauthenticated."
   * }
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function show(Request $request): JsonResponse
  {
    return response()->json($request->user());
  }

  /**
   * Actualiza la información del usuario autenticado.
   * 
   * Permite al usuario autenticado actualizar su nombre, correo electrónico o fecha de nacimiento.
   * Todos los campos son opcionales; solo se actualizarán los que se envíen.
   * 
   *  @authenticated
   *
   * @bodyParam name string optional El nuevo nombre del usuario. Example: Juan Sebastián
   * @bodyParam email string optional El nuevo correo electrónico del usuario. Debe ser único (excepto el suyo actual). Example: juan.sebastian@example.com
   * @bodyParam birthdate date optional La nueva fecha de nacimiento (YYYY-MM-DD). El usuario debe tener al menos 12 años. Example: 2005-07-20
   *
   * @response 200 {
   * "message": "Información de usuario actualizada exitosamente.",
   * "user": {
   * "id": 1,
   * "name": "Juan Sebastián",
   * "email": "juan.sebastian@example.com",
   * "created_at": "2023-10-27T10:00:00.000000Z", // Assuming creation date doesn't change
   * "updated_at": "2023-10-27T10:30:00.000000Z" // Updated timestamp
   * }
   * }
   * @response 422 scenario="Errores de Validación" {
   * "message": "Los datos proporcionados no son válidos.",
   * "errors": {
   * "email": [
   * "El correo electrónico ya está registrado por otro usuario."
   * ],
   * "birthdate": [
   * "Debes tener al menos 12 años."
   * ]
   * }
   * }
   * @response 401 {
   * "message": "Unauthenticated."
   * }
   *
   * @param  \App\Http\Requests\Auth\UpdateUserRequest  $request // Usamos el Form Request para validación
   * @return \Illuminate\Http\JsonResponse
   */
  public function update(UpdateUserRequest $request): JsonResponse
  {
    $user = $request->user();
    $user->fill($request->validated());
    $user->save();
    return response()->json([
      'message' => 'Información de usuario actualizada exitosamente.',
      'user' => $user,
    ]);
  }

  /**
   * Actualiza la contraseña del usuario autenticado.
   * 
   * Permite al usuario autenticado cambiar su contraseña.
   * Se requiere la contraseña actual para realizar el cambio.
   * 
   * @authenticated
   *
   * @bodyParam current_password string required La contraseña actual del usuario. Example: oldP@ssw0rd
   * @bodyParam password string required La nueva contraseña (mínimo 8 caracteres por defecto). Example: n3wS3cur3P@ss
   * @bodyParam password_confirmation string required Confirmación de la nueva contraseña. Debe coincidir con 'password'. Example: n3wS3cur3P@ss
   *
   * @response 200 {
   * "message": "Contraseña actualizada exitosamente."
   * }
   * @response 422 scenario="Errores de Validación" {
   * "message": "Los datos proporcionados no son válidos.",
   * "errors": {
   * "current_password": [
   * "La contraseña actual proporcionada es incorrecta."
   * ],
   * "password": [
   * "La confirmación de la nueva contraseña no coincide."
   * ]
   * }
   * }
   * @response 401 {
   * "message": "Unauthenticated."
   * }
   * 
   * @param  \App\Http\Requests\Auth\UpdatePasswordRequest  $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function updatePassword(UpdatePasswordRequest $request): JsonResponse
  {
    $user = $request->user();
    $user->password = Hash::make($request->password);
    $user->save();
    $user->tokens()->delete();

    return response()->json(['message' => 'Contraseña actualizada exitosamente.']);
  }

  /**
   * Elimina el registro del usuario autenticado.
   * 
   * Elimina la cuenta del usuario que está actualmente autenticado.
   * Esta acción es irreversible.
   * 
   * @authenticated
   *
   * @response 200 {
   * "message": "Usuario eliminado exitosamente."
   * }
   * @response 401 {
   * "message": "Unauthenticated."
   * }
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function destroy(Request $request): JsonResponse
  {
    $user = $request->user();
    $user->tokens()->delete();
    $user->delete();

    return response()->json(['message' => 'Usuario eliminado exitosamente.']);
  }
}


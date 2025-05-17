<?php

namespace App\Http\Controllers\Auth\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\UpdatePasswordRequest;
use App\Http\Requests\Auth\UpdateUserRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Muestra la información del usuario autenticado.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request): JsonResponse
    {
      return response()->json($request->user());
    }

    /**
     * Actualiza la información del usuario autenticado.
     *
     * @param  \App\Http\Requests\Api\UpdateUserRequest  $request // Usamos el Form Request para validación
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
     * @param  \App\Http\Requests\Api\UpdatePasswordRequest  $request
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
     * @param  \Illuminate\Http\Request  $request
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


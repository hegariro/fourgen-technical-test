<?php

namespace App\Policies;

use App\Models\Pet;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PetPolicy
{
  /**
   * Determine whether the user can view any models.
   *
   * @param  \App\Models\User  $user
   */
  public function viewAny(User $user): Response|bool
  {
    return Response::allow();
  }

  /**
   * Determine whether the user can view the model.
   *
   * @param  \App\Models\User  $user
   * @param  \App\Models\Pet   $pet
   */
  public function view(User $user, Pet $pet): Response|bool
  {
    return Response::allow();
  }

  /**
   * Determine whether the user can create models.
   *
   * @param  \App\Models\User  $user
   */
  public function create(User $user): Response|bool
  {
    return Response::allow();
  }

  /**
   * Determina si el usuario puede actualizar la mascota.
   *
   * @param  \App\Models\User  $user
   * @param  \App\Models\Pet   $pet
   * @return \Illuminate\Auth\Access\Response|bool
   */
  public function update(User $user, Pet $pet): Response|bool
  {
    // Un usuario puede actualizar una mascota si es el propietario de la misma.
    return $user->id === $pet->user_id
       ? Response::allow()
       : Response::deny('No autorizado para actualizar esta mascota.');
  }

  /**
   * Determina si el usuario puede eliminar la mascota.
   *
   * @param  \App\Models\User  $user
   * @param  \App\Models\Pet  $pet
   * @return \Illuminate\Auth\Access\Response|bool
   */
  public function delete(User $user, Pet $pet): Response|bool
  {
    // Un usuario puede eliminar una mascota si es el propietario de la misma.
    return $user->id === $pet->user_id
       ? Response::allow()
       : Response::deny('No autorizado para eliminar esta mascota.');
  }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Pet $pet): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Pet $pet): bool
    {
        return false;
    }
}

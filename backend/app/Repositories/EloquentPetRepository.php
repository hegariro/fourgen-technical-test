<?php

namespace App\Repositories;

use App\Models\Pet;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentPetRepository implements PetRepositoryInterface
{
  /**
   * Obtiene todas las mascotas para un usuario específico.
   *
   * @param int $userId
   * @return \Illuminate\Database\Eloquent\Collection
   */
  public function getByUser(int $userId): Collection
  {
    return Pet::where('user_id', $userId)->get();
  }

  /**
   * Obtiene todas las mascotas con paginación.
   *
   * @param int $perPage
   * @param int $page
   * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
   */
  public function getAllPaginated(int $perPage, int $page): LengthAwarePaginator
  {
    return Pet::paginate($perPage);
  }

  /**
   * Encuentra una mascota por su ID.
   *
   * @param int $id
   * @return \App\Models\Pet|null
   */
  public function findById(int $id): ?Pet
  {
    return Pet::find($id);
  }

  /**
   * Crea una nueva mascota.
   *
   * @param array $data
   * @return \App\Models\Pet
   */
  public function create(array $data): Pet
  {
    return Pet::create($data);
  }

  /**
   * Actualiza una mascota existente.
   *
   * @param \App\Models\Pet $pet La instancia de la mascota a actualizar.
   * @param array $data Los datos para actualizar.
   * @return bool True si la actualización fue exitosa, false en caso contrario.
   */
  public function update(Pet $pet, array $data): bool
  {
    return $pet->update($data);
  }

  /**
   * Elimina una mascota.
   *
   * @param \App\Models\Pet $pet La instancia de la mascota a eliminar.
   * @return bool True si la eliminación fue exitosa, false en caso contrario.
   */
  public function delete(Pet $pet): bool
  {
    return $pet->delete();
  }
}


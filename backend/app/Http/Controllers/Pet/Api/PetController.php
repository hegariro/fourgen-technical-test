<?php

namespace App\Http\Controllers\Pet\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pet\StorePetRequest;
use App\Http\Requests\Pet\UpdatePetRequest;
use App\Models\Pet;
use App\Repositories\PetRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\AuthorizationException;

/**
 * @group Gestión de Mascotas
 *
 * Endpoints para gestionar las mascotas del usuario autenticado,
 * así como listar todas las mascotas disponibles.
 * Estos endpoints requieren autenticación via Sanctum, excepto /pets/all.
 *
 */
class PetController extends Controller
{
  protected PetRepositoryInterface $petRepository;

  public function __construct(PetRepositoryInterface $petRepository)
  {
    $this->petRepository = $petRepository;
  }

  /**
   * Muestra un listado de las mascotas para el usuario autenticado.
   *
   * @authenticated
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\JsonResponse
   *
   * @response status=200 scenario="Success" {
   * "data": [
   * {
   * "id": 1,
   * "user_id": 1,
   * "name": "Firulais",
   * "species": "Dog",
   * "breed": "Labrador",
   * "age": 3,
   * "created_at": "2023-10-27T10:00:00.000000Z",
   * "updated_at": "2023-10-27T10:00:00.000000Z"
   * },
   * {
   * "id": 2,
   * "user_id": 1,
   * "name": "Michi",
   * "species": "Cat",
   * "breed": "Siamese",
   * "age": 2,
   * "created_at": "2023-10-27T10:05:00.000000Z",
   * "updated_at": "2023-10-27T10:05:00.000000Z"
   * }
   * ]
   * }
   * @response status=401 scenario="Unauthenticated" {
   * "message": "Unauthenticated."
   * }
   */
  public function index(Request $request): JsonResponse
  {
    $user = $request->user();
    $pets = $this->petRepository->getByUser($user->id);

    return response()->json($pets);
  }

  /**
   * Muestra la mascota especificada.
   *
   * Este endpoint requiere que el usuario autenticado sea el propietario de la mascota
   * o tenga permisos para verla (ver PetPolicy).
   *
   * @authenticated
   * @urlParam pet integer required El ID de la mascota a mostrar. Example: 1
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Models\Pet  $pet
   * @return \Illuminate\Http\JsonResponse
   *
   * @response status=200 scenario="Success" {
   * "id": 1,
   * "user_id": 1,
   * "name": "Firulais",
   * "species": "Dog",
   * "breed": "Labrador",
   * "age": 3,
   * "created_at": "2023-10-27T10:00:00.000000Z",
   * "updated_at": "2023-10-27T10:00:00.000000Z"
   * }
   * @response status=403 scenario="Unauthorized" {
   * "message": "This action is unauthorized."
   * }
   * @response status=404 scenario="Not Found" {
   * "message": "No query results for model [App\\Models\\Pet] 10"
   * }
   * @response status=401 scenario="Unauthenticated" {
   * "message": "Unauthenticated."
   * }
   */
  public function show(Request $request, Pet $pet): JsonResponse
  {
    $this->authorize('view', $pet);

    return response()->json($pet);
  }

  /**
     * Muestra un listado de todas las mascotas paginadas.
     *
     * Nota: Según la configuración de rutas proporcionada, este endpoint requiere autenticación,
     * a pesar del comentario original en el código del controlador.
     *
     * @authenticated
     * @queryParam limit integer Número de mascotas por página. Por defecto: 20. Example: 10
     * @queryParam page integer El número de página a recuperar. Por defecto: 1. Example: 2
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @response status=200 scenario="Success" {
     * "data": [
     * {
     * "id": 1,
     * "user_id": 1,
     * "name": "Firulais",
     * "species": "Dog",
     * "breed": "Labrador",
     * "age": 3,
     * "created_at": "2023-10-27T10:00:00.000000Z",
     * "updated_at": "2023-10-27T10:00:00.000000Z"
     * },
     * {
     * "id": 2,
     * "user_id": 2,
     * "name": "Buddy",
     * "species": "Dog",
     * "breed": null,
     * "age": 5,
     * "created_at": "2023-10-27T10:15:00.000000Z",
     * "updated_at": "2023-10-27T10:15:00.000000Z"
     * }
     * ],
     * "links": {
     * "first": "http://localhost/api/pets/all?page=1",
     * "last": "http://localhost/api/pets/all?page=3",
     * "prev": null,
     * "next": "http://localhost/api/pets/all?page=2"
     * },
     * "meta": {
     * "current_page": 1,
     * "from": 1,
     * "last_page": 3,
     * "links": [
     * {
     * "url": null,
     * "label": "&laquo; Previous",
     * "active": false
     * },
     * {
     * "url": "http://localhost/api/pets/all?page=1",
     * "label": "1",
     * "active": true
     * },
     * {
     * "url": "http://localhost/api/pets/all?page=2",
     * "label": "2",
     * "active": false
     * },
     * {
     * "url": "http://localhost/api/pets/all?page=3",
     * "label": "3",
     * "active": false
     * },
     * {
     * "url": "http://localhost/api/pets/all?page=2",
     * "label": "Next &raquo;",
     * "active": false
     * }
     * ],
     * "path": "http://localhost/api/pets/all",
     * "per_page": 20,
     * "to": 20,
     * "total": 50
     * }
     * }
     * @response status=401 scenario="Unauthenticated" {
     * "message": "Unauthenticated."
     * }
     */
  public function allPets(Request $request): JsonResponse
  {
    $limit = (int) $request->get('limit', 20);
    $page = (int) $request->get('page', 1);
    $pets = $this->petRepository->getAllPaginated($limit, $page);

    return response()->json($pets);
  }

  /**
   * Almacena una mascota recién creada.
   *
   * Asigna automáticamente la mascota al usuario autenticado.
   *
   * @authenticated
   * @bodyParam name string required El nombre de la mascota. Example: "Max"
   * @bodyParam species string required La especie de la mascota (ej. 'perro', 'gato'). Example: "Perro"
   * @bodyParam breed string La raza de la mascota. Opcional. Example: "Golden Retriever"
   * @bodyParam age integer La edad de la mascota en años. Opcional. Mínimo: 0. Example: 5
   *
   * @param  \App\Http\Requests\Api\StorePetRequest  $request
   * @return \Illuminate\Http\JsonResponse
   *
   * @response status=201 scenario="Success" {
   * "message": "Mascota creada exitosamente.",
   * "pet": {
   * "name": "Max",
   * "species": "Perro",
   * "breed": "Golden Retriever",
   * "age": 5,
   * "user_id": 1,
   * "updated_at": "2023-10-27T10:30:00.000000Z",
   * "created_at": "2023-10-27T10:30:00.000000Z",
   * "id": 3
   * }
   * }
   * @response status=422 scenario="Validation Error" {
   * "message": "The given data was invalid.",
   * "errors": {
   * "name": [
   * "El campo nombre es obligatorio."
   * ]
   * }
   * }
   * @response status=403 scenario="Unauthorized" {
   * "message": "This action is unauthorized."
   * }
   * @response status=401 scenario="Unauthenticated" {
   * "message": "Unauthenticated."
   * }
   */
  public function store(StorePetRequest $request): JsonResponse
  {
    $this->authorize('create', Pet::class);
    $pet = $this->petRepository->create($request->validated());

    return response()->json([
      'message' => 'Mascota creada exitosamente.',
      'pet' => $pet,
    ], 201);
  }

  /**
   * Actualiza la mascota especificada.
   *
   * Este endpoint requiere que el usuario autenticado sea el propietario de la mascota
   * o tenga permisos para actualizarla (ver PetPolicy).
   *
   * @authenticated
   * @urlParam pet integer required El ID de la mascota a actualizar. Ejemplo: 1
   * @bodyParam name string El nombre de la mascota. Opcional. Ejemplo: "Maximilian"
   * @bodyParam species string La especie de la mascota. Opcional. Ejemplo: "Gato"
   * @bodyParam breed string La raza de la mascota. Opcional. Ejemplo: "Siamés"
   * @bodyParam age integer La edad de la mascota. Opcional. Mínimo: 0. Ejemplo: 6
   *
   * @param  \App\Http\Requests\Api\UpdatePetRequest  $request
   * @param  \App\Models\Pet  $pet
   * @return \Illuminate\Http\JsonResponse
   *
   * @response status=200 scenario="Success" {
   * "message": "Mascota actualizada exitosamente.",
   * "pet": {
   * "id": 1,
   * "user_id": 1,
   * "name": "Maximilian",
   * "species": "Perro",
   * "breed": "Golden Retriever",
   * "age": 6,
   * "created_at": "2023-10-27T10:00:00.000000Z",
   * "updated_at": "2023-10-27T11:00:00.000000Z"
   * }
   * }
   * @response status=422 scenario="Validation Error" {
   * "message": "The given data was invalid.",
   * "errors": {
   * "age": [
   * "El campo age debe ser un número entero."
   * ]
   * }
   * }
   * @response status=403 scenario="Unauthorized" {
   * "message": "This action is unauthorized."
   * }
   * @response status=404 scenario="Not Found" {
   * "message": "No query results for model [App\\Models\\Pet] 10"
   * }
   * @response status=401 scenario="Unauthenticated" {
   * "message": "Unauthenticated."
   * }
   */
  public function update(UpdatePetRequest $request, Pet $pet): JsonResponse
  {
    $this->authorize('update', $pet);
    $updated = $this->petRepository->update($pet, $request->validated());
    $pet->refresh();

    return response()->json([
      'message' => 'Mascota actualizada exitosamente.',
      'pet' => $pet,
    ]);
  }

  /**
   * Elimina la mascota especificada.
   *
   * Este endpoint requiere que el usuario autenticado sea el propietario de la mascota
   * o tenga permisos para eliminarla (ver PetPolicy).
   *
   * @authenticated
   * @urlParam pet integer required El ID de la mascota a eliminar. Ejemplo: 1
   *
   * @param  \App\Models\Pet  $pet
   * @return \Illuminate\Http\JsonResponse
   *
   * @response status=200 scenario="Success" {
   * "message": "Mascota eliminada exitosamente."
   * }
   * @response status=403 scenario="Unauthorized" {
   * "message": "This action is unauthorized."
   * }
   * @response status=404 scenario="Not Found" {
   * "message": "No query results for model [App\\Models\\Pet] 10"
   * }
   * @response status=401 scenario="Unauthenticated" {
   * "message": "Unauthenticated."
   * }
   */
  public function destroy(Pet $pet): JsonResponse
  {
    $this->authorize('delete', $pet);
    $deleted = $this->petRepository->delete($pet);

    return response()->json(['message' => 'Mascota eliminada exitosamente.']);
  }
}


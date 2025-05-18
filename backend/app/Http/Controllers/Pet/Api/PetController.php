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
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\JsonResponse
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
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Models\Pet           $pet
   * @return \Illuminate\Http\JsonResponse
   */
  public function show(Request $request, Pet $pet): JsonResponse
  {
    $this->authorize('view', $pet);

    return response()->json($pet);
  }

  /**
   * Muestra un listado de todas las mascotas.
   * Este método no requiere autenticación.
   *
   * @return \Illuminate\Http\JsonResponse
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
   * @param  \App\Http\Requests\Api\StorePetRequest  $request
   * @return \Illuminate\Http\JsonResponse
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
   * @param  \App\Http\Requests\Api\UpdatePetRequest  $request
   * @param  \App\Models\Pet                          $pet
   * @return \Illuminate\Http\JsonResponse
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
   * @param  \App\Models\Pet  $pet
   * @return \Illuminate\Http\JsonResponse
   */
  public function destroy(Pet $pet): JsonResponse
  {
    $this->authorize('delete', $pet);
    $deleted = $this->petRepository->delete($pet);

    return response()->json(['message' => 'Mascota eliminada exitosamente.']);
  }
}


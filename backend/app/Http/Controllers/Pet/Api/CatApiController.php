<?php

namespace App\Http\Controllers\Pet\Api;

use App\Http\Controllers\Controller;
use App\Services\CatApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CatApiController extends Controller
{
  protected CatApiService $catApiService;

  /**
   * Constructor del controlador.
   * Inyecta el servicio CatApiService.
   */
  public function __construct(CatApiService $catApiService)
  {
    $this->catApiService = $catApiService;
  }

  /**
   * Lista las razas de gatos obtenidas de la API externa.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function listCatBreeds(Request $request): JsonResponse
  {
    $page = $request->get('page', 1);
    $limit = $request->get('limit', 10);
    $page = max(1, (int) $page);
    $limit = max(1, (int) $limit);
    $apiPage = $page - 1;

    $breeds = $this->catApiService->getBreeds($limit, $apiPage);

    if ($breeds === null) {
      return response()->json(
        ['message' => 'Error al obtener las razas de gatos de la API externa.'],
        500
      );
    }

    return response()->json($breeds);
  }

  /**
   * Obtiene del API externa la información aleatoria de un gato
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function getRandomCat(): JsonResponse
  {
    $cat = $this->catApiService->getRandomCat();
    if ($cat === null) {
      return response()->json(
        ['message' => 'Error al obtener aleatoriamente la información de un gato del API externa.'],
        500
      );
    }

    return response()->json($cat);
  }
}


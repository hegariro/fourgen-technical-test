<?php

namespace App\Http\Controllers\Pet\Api;

use App\Http\Controllers\Controller;
use App\Services\CatApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group API Externa de Gatos
 *
 * Endpoints para interactuar con una API externa de información sobre gatos.
 * Estos endpoints son públicos y no requieren autenticación.
 */
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
   * Lista las razas de gatos obtenidas de la API externa con paginación.
   *
   * @unauthenticated
   * @queryParam page integer El número de página a recuperar. La paginación de la API externa comienza en 0, pero este parámetro de cara al cliente comienza en 1. Por defecto: 1. Ejemplo: 2
   * @queryParam limit integer El número de razas por página. Por defecto: 5. Ejemplo: 10
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\JsonResponse
   *
   * @response status=200 scenario="Success" [
   * {
   * "id": "abys",
   * "name": "Abyssinian",
   * "temperament": "Active, Energetic, Independent, Playful, Intelligent",
   * "origin": "Egypt",
   * "country_code": "EG",
   * "description": "The Abyssinian is a native of the coastal region of the Indian Ocean...",
   * "life_span": "14 - 15",
   * "wikipedia_url": "https://en.wikipedia.org/wiki/Abyssinian_(cat)"
   * },
   * {
   * "id": "aege",
   * "name": "Aegean",
   * "temperament": "Affectionate, Social, Intelligent, Playful, Lively",
   * "origin": "Greece",
   * "country_code": "GR",
   * "description": "Native to the Greek islands known as the Cyclades in the Aegean Sea...",
   * "life_span": "9 - 12",
   * "wikipedia_url": null
   * }
   * ]
   * @response status=500 scenario="API Error" {
   * "message": "Error al obtener las razas de gatos de la API externa."
   * }
   */
  public function listCatBreeds(Request $request): JsonResponse
  {
    $page = $request->get('page', 1);
    $limit = $request->get('limit', 5);
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
   * Obtiene del API externa la información aleatoria de un gato (imagen y detalles).
   *
   * @unauthenticated
   *
   * @return \Illuminate\Http\JsonResponse
   *
   * @response status=200 scenario="Success" [
   * {
   * "id": "some_image_id",
   * "url": "https://cdn2.thecatapi.com/images/some_image_id.jpg",
   * "width": 800,
   * "height": 600,
   * "breeds": [
   * {
   * "id": "beng",
   * "name": "Bengal",
   * "temperament": "Alert, Agile, Energetic, Demanding, Intelligent",
   * "origin": "United States",
   * "country_code": "US",
   * "description": "Bengals are a lot of fun to live with...",
   * "life_span": "12 - 15",
   * "wikipedia_url": "https://en.wikipedia.org/wiki/Bengal_(cat)"
   * }
   * ]
   * }
   * ]
   * @response status=500 scenario="API Error" {
   * "message": "Error al obtener aleatoriamente la información de un gato del API externa."
   * }
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


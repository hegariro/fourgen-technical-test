<?php

namespace Tests\Feature\Pet;

use App\Models\User;
use App\Services\CatApiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CatApiTest extends TestCase
{
  use RefreshDatabase;

  /**
   * La ruta al archivo JSON de fixture de razas de gatos.
   *
   * @var string
   */
  protected string $expectedBreedsJsonPath;

  /**
   * Este método se ejecuta antes de cada método de prueba.
   *
   * @return void
   */
  protected function setUp(): void
  {
    parent::setUp();

    $this->expectedBreedsJsonPath = base_path('tests/Feature/Pet/fixtures/cat_breeds.json');
    $this->expectedCatJsonPath = base_path('tests/Feature/Pet/fixtures/image_search.json');
  }

  /**
   * Test el endpoint lista razas de gatos obtenidas del servicio externo.
   *
   * @return void
   */
  public function test_can_list_cat_breeds_from_external_api(): void
  {
    $this->assertFileExists(
      $this->expectedBreedsJsonPath, 
      "Fixture JSON file not found at: {$this->expectedBreedsJsonPath}"
    );
    $expectedBreedsJsonContent = File::get($this->expectedBreedsJsonPath);
    $expectedBreedsArray = json_decode($expectedBreedsJsonContent, true);
    $this->assertIsArray($expectedBreedsArray, "Could not decode JSON fixture file.");

    $mockCatApiService = $this->mock(CatApiService::class);
    $mockCatApiService->shouldReceive('getBreeds')
      ->once()
      ->andReturn($expectedBreedsArray);

    $response = $this->getJson('/api/cats/breeds');
    $response->assertStatus(200);

    $response->assertJson($expectedBreedsArray);
    $response->assertJsonStructure([
      '*' => ['id', 'name', 'description', 'temperament'], 
    ]);
  }

  /**
   * Test el endpoint retorna la información aleatoria de un gato
   */
  public function test_can_get_random_cat_information_from_external_api(): void
  {
    $this->assertFileExists(
      $this->expectedCatJsonPath,
      "Fixture JSON file not found at: {$this->expectedCatJsonPath}"
    );
    $expectedCatJsonContent = File::get($this->expectedCatJsonPath);
    $expectedCatArray = json_decode($expectedCatJsonContent, true);
    $this->assertIsArray($expectedCatArray, "Could not decode JSON fixture file.");

    $mockCatApiService = $this->mock(CatApiService::class);
    $mockCatApiService->shouldReceive('getRandomCat')
      ->once()
      ->andReturn($expectedCatArray);

    $response = $this->getJson('/api/cats/random');
    $response->assertStatus(200);

    $response->assertJsonStructure(['*' => ['id', 'url', 'width', 'height']]);
    $response->assertJson($expectedCatArray);
  }

  /**
   * Test el endpoint retorna un error si el servicio externo falla.
   *
   * @return void
   */
  public function test_returns_error_if_external_api_fails(): void
  {
    $this->assertFileExists(
      $this->expectedBreedsJsonPath, 
      "Fixture JSON file not found at: {$this->expectedBreedsJsonPath}"
    );
    $expectedBreedsJsonContent = File::get($this->expectedBreedsJsonPath);
    $expectedBreedsArray = json_decode($expectedBreedsJsonContent, true);
    $this->assertIsArray($expectedBreedsArray, "Could not decode JSON fixture file.");

    $mockCatApiService = $this->mock(CatApiService::class);
    $mockCatApiService->shouldReceive('getBreeds')
      ->once()
      ->andReturn(null);

    $response = $this->getJson('/api/cats/breeds');
    $response->assertStatus(500);
    $response->assertJson(['message' => 'Error al obtener las razas de gatos de la API externa.']);
  }

  /**
   * Test un usuario invitado puede acceder a la ruta pública de razas de gatos.
   * (Verifica que la ruta no requiere autenticación si la definiste como pública)
   *
   * @return void
   */
  public function test_guest_user_can_access_cat_breeds_endpoint(): void
  {
    $mockCatApiService = $this->mock(CatApiService::class);
    $mockCatApiService->shouldReceive('getBreeds')->andReturn([]);
    $response = $this->getJson('/api/cats/breeds');

    $response->assertStatus(200);
  }

  /**
   * Test que un usuario autenticado también puede acceder a la ruta pública de razas de gatos.
   * (Verifica que la autenticación no impide el acceso a la ruta pública)
   *
   * @return void
   */
  public function test_authenticated_user_can_access_cat_breeds_endpoint(): void
  {
    $user = User::factory()->create();
    Sanctum::actingAs($user, ['api']);

    $mockCatApiService = $this->mock(CatApiService::class);
    $mockCatApiService->shouldReceive('getBreeds')->andReturn([]);

    $response = $this->getJson('/api/cats/breeds');
    $response->assertStatus(200);
  }
}


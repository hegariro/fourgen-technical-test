<?php

namespace Tests\Feature\Pet;

use App\Models\Pet;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PetTest extends TestCase
{
  use RefreshDatabase;

  /**
   * Test un usuario autenticado puede listar sus propias mascotas.
   *
   * @return void
   */
  public function test_authenticated_user_can_list_their_pets(): void
  {
    $user = User::factory()->create();
    $pets = Pet::factory()->count(3)->for($user)->create();

    $otherUser = User::factory()->create();
    Pet::factory()->count(2)->for($otherUser)->create();

    Sanctum::actingAs($user, ['api']);
    $response = $this->getJson('/api/pets');
    $response->assertStatus(200);
    $response->assertJsonCount(3);

    $response->assertJsonFragment([
      'id' => $pets->first()->id,
      'name' => $pets->first()->name,
      'user_id' => $user->id,
    ]);

    $response->assertJsonStructure([
      '*' => [
        'id', 'name', 'species', 'breed', 'age', 'user_id', 'created_at', 'updated_at',
      ],
    ]);
  }

  /**
   * Test un usuario invitado no puede listar mascotas (ruta protegida).
   *
   * @return void
   */
  public function test_guest_user_cannot_list_pets(): void
  {
    Pet::factory()->count(3)->create();
    $response = $this->getJson('/api/pets');
    $response->assertStatus(401);

    $response->assertJson(['message' => 'Unauthenticated.']);
  }

  /**
   * Test un usuario autenticado puede listar todas las mascotas.
   *
   * @return void
   */
  public function test_authenticated_user_can_list_all_pets(): void
  {
    $user1 = User::factory()->create();
    Pet::factory()->count(2)->for($user1)->create();

    $user2 = User::factory()->create();
    Pet::factory()->count(3)->for($user2)->create();

    $this->assertDatabaseCount('pets', 5);

    Sanctum::actingAs($user1, ['api']);

    $response = $this->getJson('/api/pets/all');
    $response->assertStatus(200);

    $response->assertJsonStructure([
      'current_page',
      'data' => [
        '*' => [
          'id', 'name', 'species', 'breed', 'age', 'user_id', 'created_at', 'updated_at',
        ],
      ],
      'first_page_url', 'from', 'last_page', 'last_page_url', 'links', 'next_page_url',
      'path', 'per_page', 'prev_page_url', 'to', 'total',
    ]);

    $response->assertJsonFragment(['user_id' => $user1->id]);
    $response->assertJsonFragment(['user_id' => $user2->id]);
  }

  /**
   * Test que cualquier usuario puede listar todas las mascotas en una página específica.
   *
   * @return void
   */
  public function test_can_list_all_pets_on_specific_page(): void
  {
    $user = User::factory()->create();
    $pets = Pet::factory()->count(30)->for($user)->create();
    $this->assertDatabaseCount('pets', 30);

    Sanctum::actingAs($user, ['api']);

    $response = $this->getJson('/api/pets/all?page=2&limit=5');
    $response->assertStatus(200);

    $response->assertJsonStructure([
      'current_page',
      'data' => [
        '*' => [
          'id', 'name', 'species', 'breed', 'age', 'user_id', 'created_at', 'updated_at',
        ],
      ],
      'first_page_url', 'from', 'last_page', 'last_page_url', 'links', 'next_page_url',
      'path', 'per_page', 'prev_page_url', 'to', 'total',
    ]);
    $response->assertJsonCount(5, 'data');
    $response->assertJsonPath('total', 30);
  }

  /**
   * Test un usuario autenticado puede ver una de sus propias mascotas.
   *
   * @return void
   */
  public function test_authenticated_user_can_view_their_own_pet(): void
  {
    $user = User::factory()->create();
    $pet = Pet::factory()->for($user)->create();

    Sanctum::actingAs($user, ['api']);

    $response = $this->getJson('/api/pets/' . $pet->id);
    $response->assertStatus(200);

    $response->assertJsonStructure([
      'id', 'name', 'species', 'breed', 'age', 'user_id', 'created_at', 'updated_at',
    ]);

    $response->assertJsonPath('id', $pet->id);
    $response->assertJsonPath('name', $pet->name);
    $response->assertJsonPath('user_id', $user->id);
    $response->assertJsonPath('age', $pet->age);
  }

  /**
   * Test un usuario invitado no puede ver una mascota específica
   *
   * @return void
   */
  public function test_guest_user_cannot_view_specific_pet(): void
  {
    $pet = Pet::factory()->create();
    $response = $this->getJson('/api/pets/' . $pet->id);
    $response->assertStatus(401);
    $response->assertJson(['message' => 'Unauthenticated.']);
  }

  /**
   * Test un usuario autenticado puede crear una nueva mascota.
   *
   * @return void
   */
  public function test_authenticated_user_can_create_pet(): void
  {
    $user = User::factory()->create();

    $petData = [
      'name' => 'Fido',
      'species' => 'Dog',
      'breed' => 'Labrador',
      'age' => 5,
    ];

    Sanctum::actingAs($user, ['api']);

    $response = $this->postJson('/api/pets', $petData);
    $response->assertStatus(201);
    $response->assertJsonPath('message', 'Mascota creada exitosamente.');

    $this->assertDatabaseHas('pets', [
      'name' => 'Fido',
      'species' => 'Dog',
      'breed' => 'Labrador',
      'age' => 5,
      'user_id' => $user->id,
    ]);

    $response->assertJsonStructure([
      'message',
      'pet' => [
        'id', 'name', 'species', 'breed', 'age', 'user_id', 'created_at', 'updated_at',
      ],
    ]);
    $response->assertJsonPath('pet.name', 'Fido');
    $response->assertJsonPath('pet.user_id', $user->id);
  }

  /**
   * Test un usuario autenticado no puede crear una mascota con datos inválidos.
   *
   * @return void
   */
  public function test_authenticated_user_cannot_create_pet_with_invalid_data(): void
  {
    $user = User::factory()->create();
    Sanctum::actingAs($user, ['api']);

    // Datos inválidos para crear una mascota (faltan 'name' y 'species')
    $invalidData = [
      'breed' => 'Mixed',
      'age' => 2,
    ];
    $response = $this->postJson('/api/pets', $invalidData);
    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['name', 'species']);
    $this->assertDatabaseCount('pets', 0);
  }

  /**
   * Test un usuario invitado no puede crear una mascota (ruta protegida).
   *
   * @return void
   */
  public function test_guest_user_cannot_create_pet(): void
  {
    $petData = [
      'name' => 'Fido',
      'species' => 'Dog',
    ];
    $response = $this->postJson('/api/pets', $petData);
    $response->assertStatus(401);
    $response->assertJson(['message' => 'Unauthenticated.']);
    $this->assertDatabaseCount('pets', 0);
  }

  /**
   * Test un usuario autenticado puede actualizar una de sus propias mascotas.
   *
   * @return void
   */
  public function test_authenticated_user_can_update_their_own_pet(): void
  {
    $user = User::factory()->create();
    $pet = Pet::factory()->for($user)->create([
      'name' => 'Old Name',
      'species' => 'Old Species',
    ]);
    Sanctum::actingAs($user, ['api']);

    $newData = [
      'name' => 'New Name',
      'species' => 'New Species',
      'age' => 7,
    ];
    $response = $this->putJson('/api/pets/' . $pet->id, $newData);
    $response->assertStatus(200);
    $response->assertJsonPath('message', 'Mascota actualizada exitosamente.');

    $this->assertDatabaseHas('pets', [
      'id' => $pet->id,
      'name' => 'New Name',
      'species' => 'New Species',
      'age' => 7,
      'user_id' => $user->id,
    ]);

    $response->assertJsonPath('pet.id', $pet->id);
    $response->assertJsonPath('pet.name', 'New Name');
    $response->assertJsonPath('pet.species', 'New Species');
    $response->assertJsonPath('pet.age', 7);
  }

  /**
   * Test un usuario autenticado NO puede actualizar una mascota que no le pertenece.
   * Esto prueba la Policy.
   *
   * @return void
   */
  public function test_authenticated_user_cannot_update_another_users_pet(): void
  {
    $user = User::factory()->create();

    $otherUser = User::factory()->create();
    $otherPet = Pet::factory()->for($otherUser)->create([
      'name' => 'Original Name',
    ]);

    Sanctum::actingAs($user, ['api']);

    $newData = [
      'name' => 'Attempted Update',
    ];
    $response = $this->putJson('/api/pets/' . $otherPet->id, $newData);
    $response->assertStatus(403);
    $response->assertJson(['message' => 'No autorizado para actualizar esta mascota.']);

    $this->assertDatabaseHas('pets', [
      'id' => $otherPet->id,
      'name' => 'Original Name',
    ]);
  }

  /**
   * Test un usuario autenticado no puede actualizar una mascota con datos inválidos.
   * (Ej: especie vacía si es obligatoria, aunque en UpdatePetRequest es sometimes)
   * Aquí probaremos un tipo de dato incorrecto o una edad negativa.
   *
   * @return void
   */
  public function test_authenticated_user_cannot_update_pet_with_invalid_data(): void
  {
    $user = User::factory()->create();
    $pet = Pet::factory()->for($user)->create();

    Sanctum::actingAs($user, ['api']);

    $invalidData = [
      'age' => -5,
    ];
    $response = $this->putJson('/api/pets/' . $pet->id, $invalidData);
    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['age']);

    $this->assertDatabaseMissing('pets', [
      'id' => $pet->id,
      'age' => -5,
    ]);

    $pet->refresh();
    $this->assertEquals($pet->age, $pet->getOriginal('age'));
  }

  /**
   * Test un usuario invitado no puede actualizar una mascota (ruta protegida).
   *
   * @return void
   */
  public function test_guest_user_cannot_update_pet(): void
  {
    $pet = Pet::factory()->create([
      'name' => 'Original Name',
    ]);

    $newData = [
      'name' => 'Attempted Update',
    ];
    $response = $this->putJson('/api/pets/' . $pet->id, $newData);
    $response->assertStatus(401);
    $response->assertJson(['message' => 'Unauthenticated.']);

    $this->assertDatabaseHas('pets', [
      'id' => $pet->id,
      'name' => 'Original Name',
    ]);
  }

  /**
   * Test un usuario autenticado puede eliminar una de sus propias mascotas.
   *
   * @return void
   */
  public function test_authenticated_user_can_delete_their_own_pet(): void
  {
    $user = User::factory()->create();
    $pet = Pet::factory()->for($user)->create();
    $this->assertDatabaseHas('pets', ['id' => $pet->id]);

    Sanctum::actingAs($user, ['api']);

    $response = $this->deleteJson('/api/pets/' . $pet->id);
    $response->assertStatus(200);
    $response->assertJson(['message' => 'Mascota eliminada exitosamente.']);

    $this->assertDatabaseMissing('pets', ['id' => $pet->id]);
  }

  /**
   * Test un usuario autenticado NO puede eliminar una mascota que no le pertenece.
   * Esto prueba la Policy.
   *
   * @return void
   */
  public function test_authenticated_user_cannot_delete_another_users_pet(): void
  {
    $user = User::factory()->create();

    $otherUser = User::factory()->create();
    $otherPet = Pet::factory()->for($otherUser)->create();

    $this->assertDatabaseHas('pets', ['id' => $otherPet->id]);

    Sanctum::actingAs($user, ['api']);

    $response = $this->deleteJson('/api/pets/' . $otherPet->id);
    $response->assertStatus(403);
    $response->assertJson(['message' => 'No autorizado para eliminar esta mascota.']);
    $this->assertDatabaseHas('pets', ['id' => $otherPet->id]);
  }

  /**
   * Test un usuario invitado no puede eliminar una mascota (ruta protegida).
   *
   * @return void
   */
  public function test_guest_user_cannot_delete_pet(): void
  {
    $pet = Pet::factory()->create();
    $this->assertDatabaseHas('pets', ['id' => $pet->id]);

    $response = $this->deleteJson('/api/pets/' . $pet->id);
    $response->assertStatus(401);
    $response->assertJson(['message' => 'Unauthenticated.']);
    $this->assertDatabaseHas('pets', ['id' => $pet->id]);
  }
}

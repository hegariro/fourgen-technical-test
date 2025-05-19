<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;

class LoginTest extends TestCase
{
  use RefreshDatabase;

  /**
   * Test that a user can log in via the API with correct credentials.
   *
   * @return void
   */
  public function test_users_can_login_via_api(): void
  {
    $password = 'correct-password';
    $user = User::factory()->create([
      'password' => Hash::make($password),
    ]);

    $credentials = [
      'email' => $user->email,
      'password' => $password,
    ];

    $response = $this->postJson('/api/login', $credentials);
    $response->assertStatus(200);
    $response->assertJsonStructure([
      'message',
      'access_token',
      'token_type',
    ]);

    $response->assertJsonPath('message', 'Login exitoso.');
    $response->assertJsonPath('token_type', 'Bearer');
    $this->assertNotEmpty($response->json('access_token'));

    $this->assertDatabaseHas('personal_access_tokens', [
      'tokenable_type' => get_class($user),
      'tokenable_id' => $user->id,
      'name' => 'auth_token',
    ]);
  }

  /**
   * Test that a user cannot log in via the API with incorrect credentials.
   *
   * @return void
   */
  public function test_users_cannot_login_with_invalid_credentials_via_api(): void
  {
    $password = 'correct-password';
    $user = User::factory()->create([
      'password' => Hash::make($password),
    ]);

    $credentials = [
      'email' => $user->email,
      'password' => 'incorrect-password', // Incorrect password
    ];

    $response = $this->postJson('/api/login', $credentials);
    $response->assertStatus(422);

    $response->assertJsonValidationErrors(['email']);
    $response->assertJsonPath('errors.email.0', trans('auth.failed'));
    $this->assertDatabaseMissing('personal_access_tokens', [
      'tokenable_type' => get_class($user),
      'tokenable_id' => $user->id,
    ]);
  }

  /**
   * Test that a user cannot log in via the API with missing required fields.
   *
   * @return void
   */
  public function test_users_cannot_login_with_missing_fields_via_api(): void
  {
    $response = $this->postJson('/api/login', []);
    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['email', 'password']);

    $this->assertDatabaseCount('users', 0);
    $this->assertDatabaseCount('personal_access_tokens', 0);
  }

  /**
   * Test that an authenticated user can log out via the API.
   *
   * @return void
   */
  public function test_authenticated_users_can_logout_via_api(): void
  {
    $user = User::factory()->create();
    $token = $user->createToken('auth_token')->plainTextToken;
    $this->assertDatabaseHas('personal_access_tokens', [
      'tokenable_type' => get_class($user),
      'tokenable_id' => $user->id,
      'name' => 'auth_token',
    ]);

    $response = $this->postJson('/api/logout', [], [
      'Authorization' => 'Bearer ' . $token,
    ]);
    $response->assertStatus(200);
    $response->assertJson(['message' => 'Sesión cerrada exitosamente.']);

    $this->assertDatabaseMissing('personal_access_tokens', [
      'tokenable_type' => get_class($user),
      'tokenable_id' => $user->id,
      'name' => 'auth_token',
    ]);

    $user->refresh();
    $this->assertCount(0, $user->tokens);
  }

  /**
   * Test that a guest user cannot access the API logout route.
   *
   * @return void
   */
  public function test_guest_users_cannot_logout_via_api(): void
  {
    $response = $this->postJson('/api/logout');
    $response->assertStatus(401);

    $response->assertJson(['message' => 'Unauthenticated.']);
  }
}


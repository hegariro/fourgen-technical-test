<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserTest extends TestCase
{
  use RefreshDatabase;

  /**
   * Setup the test environment.
   *
   * @return void
   */
  protected function setUp(): void
  {
    parent::setUp();
  }


  /**
   * Test that an authenticated user can view their own information via API.
   *
   * @return void
   */
  public function test_authenticated_user_can_view_their_info(): void
  {
    $user = User::factory()->create([
      'birthdate' => '1990-05-15',
    ]);

    Sanctum::actingAs($user, ['api']);
    $response = $this->getJson('/api/user');
    $response->assertStatus(200);

    $response->assertJsonStructure([
      'id',
      'name',
      'email',
      'birthdate',
      'created_at',
      'updated_at',
      'email_verified_at',
    ]);

    $response->assertJsonPath('id', $user->id);
    $response->assertJsonPath('name', $user->name);
    $response->assertJsonPath('email', $user->email);
    $response->assertJsonPath('birthdate', Carbon::parse('1990-05-15')->format('Y-m-d\TH:i:s.u\Z'));
  }

  /**
   * Test that a guest user cannot view user information via API.
   *
   * @return void
   */
  public function test_guest_user_cannot_view_user_info(): void
  {
    $response = $this->getJson('/api/user');
    $response->assertStatus(401);
    $response->assertJson(['message' => 'Unauthenticated.']);
  }

  /**
   * Test that an authenticated user can update their own information via API.
   *
   * @return void
   */
  public function test_authenticated_user_can_update_their_info(): void
  {
    $user = User::factory()->create([
       'name' => 'Old Name',
       'email' => 'old@example.com',
    ]);
    Sanctum::actingAs($user, ['api']);
    $newData = [
      'name' => 'New Name',
      'email' => 'new@example.com',
      'birthdate' => '1995-10-20',
    ];

    $response = $this->putJson('/api/user', $newData);
    $response->assertStatus(200);
    $response->assertJsonPath('message', 'Información de usuario actualizada exitosamente.');

    $this->assertDatabaseHas('users', [
      'id' => $user->id,
      'name' => 'New Name',
      'email' => 'new@example.com',
    ]);

    $response->assertJsonPath('user.id', $user->id);
    $response->assertJsonPath('user.name', 'New Name');
    $response->assertJsonPath('user.email', 'new@example.com');
    $response->assertJsonPath('user.birthdate', Carbon::parse('1995-10-20')->format('Y-m-d\TH:i:s.u\Z'));
  }

  /**
   * Test that an authenticated user cannot update info with invalid data (e.g., duplicate email).
   *
   * @return void
   */
  public function test_authenticated_user_cannot_update_info_with_invalid_data(): void
  {
    $user = User::factory()->create([
      'email' => 'user@example.com',
    ]);
    $anotherUser = User::factory()->create([
      'email' => 'duplicate@example.com',
    ]);
    Sanctum::actingAs($user, ['api']);
    $invalidData = [
      'email' => 'duplicate@example.com', // This email already exists
    ];
    $response = $this->putJson('/api/user', $invalidData);
    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['email']);

    $response->assertJsonPath(
      'errors.email.0', 
      'El correo electrónico ya está registrado por otro usuario.'
    );

    $this->assertDatabaseHas('users', [
      'id' => $user->id,
      'email' => 'user@example.com',
    ]);
  }

  /**
   * Test that a guest user cannot update user information via API.
   *
   * @return void
   */
  public function test_guest_user_cannot_update_user_info(): void
  {
    $user = User::factory()->create();
    $response = $this->putJson('/api/user', ['name' => 'Attempted Update']);
    $response->assertStatus(401);
    $response->assertJson(['message' => 'Unauthenticated.']);

    $this->assertDatabaseHas('users', [
      'id' => $user->id,
      'name' => $user->name, // Should still be the original name
    ]);
  }

  /**
   * Test that an authenticated user can update their password via API.
   *
   * @return void
   */
  public function test_authenticated_user_can_update_their_password(): void
  {
    $currentPassword = 'old-secret-password';
    $user = User::factory()->create([
      'password' => Hash::make($currentPassword),
    ]);
    Sanctum::actingAs($user, ['api']);
    $newPassword = 'new-secret-password';
    $passwordData = [
      'current_password' => $currentPassword,
      'password' => $newPassword,
      'password_confirmation' => $newPassword,
    ];
    $response = $this->putJson('/api/user/password', $passwordData);
    $response->assertStatus(200);
    $response->assertJson(['message' => 'Contraseña actualizada exitosamente.']);

    $user->refresh();
    $this->assertTrue(Hash::check($newPassword, $user->password));
    $this->assertCount(0, $user->tokens);
  }

  /**
   * Test that an authenticated user cannot update password with incorrect current password.
   *
   * @return void
   */
  public function test_authenticated_user_cannot_update_password_with_incorrect_current_password(): void
  {
    $currentPassword = 'old-secret-password';
    $user = User::factory()->create([
      'password' => Hash::make($currentPassword),
    ]);
    Sanctum::actingAs($user, ['api']);
    $passwordData = [
      'current_password' => 'wrong-password',
      'password' => 'new-secret-password',
      'password_confirmation' => 'new-secret-password',
    ];
    $response = $this->putJson('/api/user/password', $passwordData);
    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['current_password']);

    $response->assertJsonPath(
      'errors.current_password.0', 
      'La contraseña actual proporcionada es incorrecta.'
    );

    $user->refresh();
    $this->assertTrue(Hash::check($currentPassword, $user->password));
  }

  /**
   * Test that an authenticated user cannot update password with invalid new password (e.g., not confirmed).
   *
   * @return void
   */
  public function test_authenticated_user_cannot_update_password_with_invalid_new_password(): void
  {
    $currentPassword = 'old-secret-password';
    $user = User::factory()->create([
      'password' => Hash::make($currentPassword),
    ]);
    Sanctum::actingAs($user, ['api']);
    $passwordData = [
      'current_password' => $currentPassword,
      'password' => 'new-secret-password',
      'password_confirmation' => 'mismatch', // Does not match password
    ];
    $response = $this->putJson('/api/user/password', $passwordData);
    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['password']);

    $response->assertJsonPath(
      'errors.password.0', 
      'La confirmación de la nueva contraseña no coincide.'
    );

    $user->refresh();
    $this->assertTrue(Hash::check($currentPassword, $user->password)); // Should still be the old password
  }

  /**
   * Test that a guest user cannot update password via API.
   *
   * @return void
   */
  public function test_guest_user_cannot_update_password(): void
  {
    $user = User::factory()->create();
    $originalHashedPassword = $user->password;
    $response = $this->putJson('/api/user/password', [
      'current_password' => 'any-password',
      'password' => 'new-password',
      'password_confirmation' => 'new-password',
    ]);
    $response->assertStatus(401);
    $response->assertJson(['message' => 'Unauthenticated.']);

    $user->refresh();
    $this->assertEquals($originalHashedPassword, $user->password);
  }

  /**
   * Test that an authenticated user can delete their own account via API.
   *
   * @return void
   */
  public function test_authenticated_user_can_delete_their_account(): void
  {
    $user = User::factory()->create();
    $token = $user->createToken('auth_token')->plainTextToken;
    $this->assertDatabaseHas('users', ['id' => $user->id]);
    $this->assertDatabaseHas('personal_access_tokens', [
      'tokenable_type' => get_class($user),
      'tokenable_id' => $user->id,
    ]);
    Sanctum::actingAs($user, ['api']);
    $response = $this->deleteJson('/api/user', [], [
       'Authorization' => 'Bearer ' . $token,
    ]);

    $response->assertStatus(200);
    $response->assertJson(['message' => 'Usuario eliminado exitosamente.']);
    $this->assertDatabaseMissing('users', ['id' => $user->id]);

    $this->assertDatabaseMissing('personal_access_tokens', [
      'tokenable_type' => get_class($user),
      'tokenable_id' => $user->id,
    ]);
  }

  /**
   * Test that a guest user cannot delete user account via API.
   *
   * @return void
   */
  public function test_guest_user_cannot_delete_user_account(): void
  {
    $user = User::factory()->create();
    $this->assertDatabaseHas('users', ['id' => $user->id]);
    $response = $this->deleteJson('/api/user');
    $response->assertStatus(401);
    $response->assertJson(['message' => 'Unauthenticated.']);

    $this->assertDatabaseHas('users', ['id' => $user->id]);
  }
}


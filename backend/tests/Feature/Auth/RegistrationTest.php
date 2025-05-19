<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testear el registro de un usuario exitoso.
     *
     * @return void
     */
    public function test_new_users_can_register(): void
    {
        Event::fake();

        // Datos válidos para el registro
        $userData = [
            'name' => 'Another Test User',
            'email' => 'another.test.user@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'birthdate' => '2000-11-01',
        ];

        $response = $this->postJson('/api/register', $userData);
        $response->assertStatus(201);

        $this->assertDatabaseHas('users', [
            'email' => 'another.test.user@example.com',
            'name' => 'Another Test User',
            'birthdate' => '2000-11-01 00:00:00',
        ]);

        $response->assertJsonStructure([
            'message',
            'user' => ['id', 'name', 'email', 'birthdate', 'created_at', 'updated_at'],
            'access_token',
            'token_type',
        ]);

        if (isset($response->json()['access_token'])) {
          $response->assertJsonPath('message', 'Usuario registrado exitosamente.');
          $response->assertJsonPath('token_type', 'Bearer');
          $response->assertJsonPath('user.id', 1);
          $response->assertJsonPath('user.name', 'Another Test User');
          $response->assertJsonPath('user.email', 'another.test.user@example.com');
          $response->assertJsonPath('user.birthdate', Carbon::parse('2000-11-01')
            ->format('Y-m-d\TH:i:s.u\Z'));
          $this->assertNotEmpty($response->json('access_token'));
        }

        $user = User::where('email', $userData['email'])->first();
    }

    /**
     * Testear que el registro falla con datos inválidos (ej: email duplicado).
     *
     * @return void
     */
    public function test_users_cannot_register_with_duplicate_email(): void
    {
        $existingUser = User::factory()->create([
            'email' => 'existing@example.com',
        ]);

        // Datos para el registro con el email duplicado
        $userData = [
            'name' => 'Another User',
            'email' => 'existing@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'birthdate' => '2001-01-01',
        ];

        $response = $this->postJson('/api/register', $userData);
        // HTTP 422 (Unprocessable Entity)
        $response->assertStatus(422);

        $this->assertDatabaseMissing('users', [
          'email' => 'existing@example.com',
          'name' => 'Another User',
        ]);

        $response->assertJsonValidationErrors(['email']);
    }

    /**
     * Testear que el registro falla con campos obligatorios faltantes.
     *
     * @return void
     */
    public function test_users_cannot_register_with_missing_fields(): void
    {
        $userData = [
            'name' => 'Incomplete User',
            'birthdate' => '2002-01-01',
            // Faltan email, password, password_confirmation
        ];

        $response = $this->postJson('/api/register', $userData);
        // HTTP 422 (Unprocessable Entity)
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email', 'password']);

        $this->assertDatabaseMissing('users', [
          'name' => 'Incomplete User',
          'birthdate' => '2002-01-01',
        ]);
    }

     /**
     * Testear que el registro falla si las contraseñas no coinciden.
     *
     * @return void
     */
    public function test_users_cannot_register_if_passwords_do_not_match(): void
    {
        $userData = [
            'name' => 'Mismatch User',
            'email' => 'mismatch@example.com',
            'password' => 'password1',
            'password_confirmation' => 'password2', // No coincide
            'birthdate' => '2003-01-01',
        ];

        $response = $this->postJson('/api/register', $userData);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['password']);

        $this->assertDatabaseMissing('users', ['email' => 'mismatch@example.com']);
    }

    /**
     * Testear que el registro falla si el usuario es menor de 12 años.
     *
     * @return void
     */
    public function test_users_cannot_register_if_under_12_years_old(): void
    {
        $birthdateUnder12 = now()->subYears(11)->format('Y-m-d');

        $userData = [
            'name' => 'Young User',
            'email' => 'young@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'birthdate' => $birthdateUnder12,
        ];

        $response = $this->postJson('/api/register', $userData);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['birthdate']);

        $this->assertDatabaseMissing('users', ['email' => 'young@example.com']);
    }

     /**
     * Testear que el registro falla con un formato de email inválido.
     *
     * @return void
     */
    public function test_users_cannot_register_with_invalid_email_format(): void
    {
        $userData = [
            'name' => 'Invalid Email User',
            'email' => 'invalid-email', // Formato inválido
            'password' => 'password',
            'password_confirmation' => 'password',
            'birthdate' => '2000-01-01',
        ];

        $response = $this->postJson('/api/register', $userData);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);

        $this->assertDatabaseMissing('users', ['email' => 'invalid-email']);
    }
}


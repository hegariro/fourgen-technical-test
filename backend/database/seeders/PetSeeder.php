<?php

namespace Database\Seeders;

use App\Models\Pet;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PetSeeder extends Seeder
{
  /**
   * Run the database seeds.
   * Ejecuta los seeders de la base de datos.
   */
  public function run(): void
  {
    // Asegurarse de que existan usuarios antes de crear mascotas.
    if (User::count() === 0) {
      // Si no existen usuarios, crear algunos primero.
      User::factory()->count(10)->create();
    }

    // Opcional: Crear mascotas para un usuario específico.
    $user = User::find(1); // Find user with ID 1
    Pet::factory()->count(5)->for($user)->create(); // Create 5 pets for this user
  }
}

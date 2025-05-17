<?php

namespace Database\Factories;

use App\Models\Pet;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pet>
 */
class PetFactory extends Factory
{
  /**
   * The name of the corresponding model.
   * El nombre del modelo correspondiente.
   *
   * @var string
   */
  protected $model = Pet::class;

  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    // Obtener un ID de usuario existente de forma aleatoria.
    $userId = User::inRandomOrder()->first()->id ?? User::factory()->create()->id;

    return [
      'name' => $this->faker->firstName(),
      'species' => $this->faker->randomElement(['Dog', 'Cat']),
      'breed' => $this->faker->word(),
      'age' => $this->faker->numberBetween(1, 15),
      'user_id' => $userId,
    ];
  }
}

<?php

namespace App\Providers;

use App\Models\Pet;
use App\Policies\PetPolicy;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
  /**
   * The model to policy mappings for the application.
   *
   * @var array<class-string, class-string>
   */
  protected $policies = [
    // Mapea el modelo Pet a la PetPolicy
    Pet::class => PetPolicy::class,
  ];

  /**
   * Register any application services.
   */
  public function register(): void
  {
    //
  }


  /**
   * Bootstrap services.
   */
  public function boot(): void
  {
    //
  }
}

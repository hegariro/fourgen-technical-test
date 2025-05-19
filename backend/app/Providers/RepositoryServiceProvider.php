<?php

namespace App\Providers;

use App\Repositories\EloquentPetRepository;
use App\Repositories\PetRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
  public function register()
  {
    $this->app->bind(PetRepositoryInterface::class, EloquentPetRepository::class);
  }

  public function boot()
  {
    //
  }
}


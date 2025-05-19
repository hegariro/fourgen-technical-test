<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pet extends Model
{
  use HasFactory;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'name',
    'species',
    'breed',
    'age',
    'user_id',
  ];

  /**
   * The attributes that should be cast.
   *
   * @return array<string, string>
   */
  protected function casts(): array
  {
    return [
      'age' => 'integer',
    ];
  }

  /**
   * Get the user that owns the pet.
   * Obtiene el usuario al que pertenece esta mascota.
   */
  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class);
  }
}

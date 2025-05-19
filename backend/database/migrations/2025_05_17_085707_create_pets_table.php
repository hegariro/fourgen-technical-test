<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('pets', function (Blueprint $table) {
      $table->id();
      $table->string('name');
      $table->string('species'); // Especie de la mascota (ej: perro, gato)
      $table->string('breed')->nullable(); // Raza de la mascota (puede ser nulo si no se conoce)
      $table->integer('age')->nullable(); // Edad de la mascota (puede ser nulo)

      $table->foreignId('user_id')
        ->constrained() // Crea la restricción de clave foránea
        ->onDelete('cascade'); // Opcional: si el usuario es eliminado, sus mascotas también lo son

      $table->timestamps(); // Columnas created_at y updated_at
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('pets');
  }
};

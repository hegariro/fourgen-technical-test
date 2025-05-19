<?php

namespace App\Http\Requests\Pet;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePetRequest extends FormRequest
{
  /**
   * Determina si el usuario está autorizado para hacer esta solicitud.
   *
   * @return bool
   */
  public function authorize(): bool
  {
    return true;
  }

  /**
   * Obtiene las reglas de validación que se aplican a la solicitud.
   *
   * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
   */
  public function rules(): array
  {
    return [
      'name' => ['sometimes', 'string', 'max:255'],
      'species' => ['sometimes', 'string', 'max:255'],
      'breed' => ['sometimes', 'nullable', 'string', 'max:255'],
      'age' => ['sometimes', 'nullable', 'integer', 'min:0'],
    ];
  }

  /**
   * Obtiene atributos personalizados para los errores del validador.
   *
   * @return array<string, string>
   */
  public function attributes(): array
  {
    return [
      'name' => 'nombre',
      'species' => 'especie',
      'breed' => 'raza',
      'age' => 'edad',
    ];
  }

  /**
   * Obtiene mensajes personalizados para los errores del validador.
   *
   * @return array<string, string>
   */
  public function messages(): array
  {
    return [
      'name.sometimes' => 'El nombre de la mascota debe ser una cadena de texto.',
      'species.sometimes' => 'La especie de la mascota debe ser una cadena de texto.',
      'age.integer' => 'La edad debe ser un número entero.',
      'age.min' => 'La edad no puede ser negativa.',
    ];
  }
}


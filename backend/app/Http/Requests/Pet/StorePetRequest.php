<?php

namespace App\Http\Requests\Pet;

use Illuminate\Foundation\Http\FormRequest;

class StorePetRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   */
  public function authorize(): bool
  {
    return true;
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
   */
  public function rules(): array
  {
    return [
      'name' => ['required', 'string', 'max:255'],
      'species' => ['required', 'string', 'max:255'],
      'breed' => ['nullable', 'string', 'max:255'],
      'age' => ['nullable', 'integer', 'min:0'],
      'user_id' => ['required', 'exists:users,id'],
    ];
  }

  /**
   * Prepara los datos para la validación.
   * Aquí podemos añadir el user_id del usuario autenticado antes de validar.
   */
  protected function prepareForValidation(): void
  {
    // Fusiona el user_id del usuario autenticado en los datos de la solicitud.
    $this->merge([
      'user_id' => $this->user()->id,
    ]);
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
      'user_id' => 'propietario',
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
      'name.required' => 'El nombre de la mascota es obligatorio.',
      'species.required' => 'La especie de la mascota es obligatoria.',
      'age.integer' => 'La edad debe ser un número entero.',
      'age.min' => 'La edad no puede ser negativa.',
      'user_id.required' => 'El usuario debe iniciar sesión.',
      'user_id.exists' => 'El usuario debe iniciar sesión.',
    ];
  }
}


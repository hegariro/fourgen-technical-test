<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;

class UpdatePasswordRequest extends FormRequest
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
        'current_password' => ['required', 'string'],
        'password' => ['required', 'string', 'confirmed', Password::defaults()],
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
        'current_password' => 'contraseña actual',
        'password' => 'nueva contraseña',
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
        'current_password.required' => 'La contraseña actual es obligatoria.',
        'password.required' => 'La nueva contraseña es obligatoria.',
        'password.confirmed' => 'La confirmación de la nueva contraseña no coincide.',
      ];
    }

    /**
     * Este método se llama automáticamente después de las reglas de validación básicas.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function withValidator($validator): void
    {
      $validator->after(function ($validator) {
        $user = $this->user();
        if (! Hash::check($this->current_password, $user->password)) {
          $validator->errors()
            ->add('current_password', __('La contraseña actual proporcionada es incorrecta.'));
        }
      });
    }
}


<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
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
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
      return [
        'email' => 'correo electrónico',
        'password' => 'contraseña',
      ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
      return [
        'email.email' => 'El correo electrónico no tiene la estructura adecuada.',
        'email.required' => 'El correo electrónico es obligatorio.',
        'password.required' => 'La contraseña no puede estar vacia.',
      ];
    }

    /**
     * Intenta autenticar las credenciales de la solicitud.
     */
    public function authenticate(): void
    {
       $this->ensureIsNotRateLimited();
       if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
         // Si Auth::attempt() retorna false, significa que las credenciales son incorrectas.
         // Incrementa el contador de intentos fallidos para esta clave (email + IP)
         RateLimiter::hit($this->throttleKey());

         throw ValidationException::withMessages([
           'email' => trans('auth.failed'),
         ]);
       }

       // limpia los intentos fallidos para esta clave.
       RateLimiter::clear($this->throttleKey());
    }

    /**
     * Asegura que la solicitud de login no esté limitada por intentos.
     */
    public function ensureIsNotRateLimited(): void
    {
      // Verifica si hay demasiados intentos fallidos para esta clave (email + IP).
      if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
        return;
      }

      // Se calcula el tiempo entre intentos para limitar las solicitudes por minuto
      $seconds = RateLimiter::availableIn($this->throttleKey());
      // Lanza una excepción de validación indicando demasiados intentos.
      throw ValidationException::withMessages([
        'email' => trans('auth.throttle', [
          'seconds' => $seconds,
          'minutes' => ceil($seconds / 60),
        ]),
      ]);
    }

    /**
     * Obtiene la clave de limitación de intentos para la solicitud.
     */
    public function throttleKey(): string
    {
      // Genera una clave única basada en el email y la IP
      return Str::transliterate(Str::lower($this->input('email')).'|'.$this->ip());
    }
}

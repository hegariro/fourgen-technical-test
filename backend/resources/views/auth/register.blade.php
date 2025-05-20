@extends('layouts.app')

@section('content')
  <div class="container mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-center">
      <div class="w-full md:w-2/3 lg:w-1/2 xl:w-2/5">
        <div class="card float-animation">
          <div class="card-header flex items-center">
            <span class="pet-icon dog mr-2"></span>
            {{ __('Únete a PetShop') }}
          </div>

          <div class="card-body">
            <div class="mb-6 text-center">
              {{-- Se agregó un toque de color terciario y un pequeño shadow-sm al título para un estilo más amigable --}}
              <h2 class="text-2xl font-bold text-tertiary shadow-sm-text">¡Crea tu cuenta!</h2>
              <p class="text-gray-500">Y accede a ofertas exclusivas para nuestros miembros</p>
            </div>

            <form action="{{ route('register') }}" method="POST">
              @csrf

              <div class="mb-6">
                <label class="mb-2 block font-semibold text-gray-700" for="name">{{ __('Nombre') }}</label>
                <div>
                  {{-- Se agregó la clase shadow-sm aquí --}}
                  <input autocomplete="name" autofocus class="form-control @error('name') is-invalid @enderror w-full shadow-sm"
                    id="name" name="name" placeholder="Tu nombre completo" required type="text"
                    value="{{ old('name') }}" />
                  @error('name')
                    <span class="invalid-feedback mt-1 block" role="alert">
                      <strong>{{ $message }}</strong>
                    </span>
                  @enderror
                </div>
              </div>

              <div class="mb-6">
                <label class="mb-2 block font-semibold text-gray-700"
                  for="email">{{ __('Correo Electrónico') }}</label>
                <div>
                  {{-- Se agregó la clase shadow-sm aquí --}}
                  <input autocomplete="email" class="form-control @error('email') is-invalid @enderror w-full shadow-sm"
                    id="email" name="email" placeholder="tu@email.com" required type="email"
                    value="{{ old('email') }}" />
                  @error('email')
                    <span class="invalid-feedback mt-1 block" role="alert">
                      <strong>{{ $message }}</strong>
                    </span>
                  @enderror
                </div>
              </div>

              <div class="mb-6">
                <label class="mb-2 block font-semibold text-gray-700"
                  for="birthdate">{{ __('Fecha de Nacimiento') }}</label>
                <div>
                  {{-- Se agregó la clase shadow-sm aquí --}}
                  <input class="form-control @error('birthdate') is-invalid @enderror w-full shadow-sm" id="birthdate"
                    name="birthdate" required type="date" value="{{ old('birthdate') }}" />
                  @error('birthdate')
                    <span class="invalid-feedback mt-1 block" role="alert">
                      <strong>{{ $message }}</strong>
                    </span>
                  @enderror
                </div>
              </div>

              <div class="mb-6">
                <label class="mb-2 block font-semibold text-gray-700" for="password">{{ __('Contraseña') }}</label>
                <div>
                  {{-- Se agregó la clase shadow-sm aquí --}}
                  <input autocomplete="new-password" class="form-control @error('password') is-invalid @enderror w-full shadow-sm"
                    id="password" name="password" placeholder="Mínimo 8 caracteres" required type="password" />
                  @error('password')
                    <span class="invalid-feedback mt-1 block" role="alert">
                      <strong>{{ $message }}</strong>
                    </span>
                  @enderror
                </div>
              </div>

              <div class="mb-6">
                <label class="mb-2 block font-semibold text-gray-700"
                  for="password-confirm">{{ __('Confirmar Contraseña') }}</label>
                <div>
                  {{-- Se agregó la clase shadow-sm aquí --}}
                  <input autocomplete="new-password" class="form-control w-full shadow-sm" id="password-confirm"
                    name="password_confirmation" placeholder="Repite tu contraseña" required type="password" />
                </div>
              </div>

              <div class="mb-6">
                <div class="flex items-start">
                  {{-- Un pequeño ajuste de estilo al checkbox para que sea más coherente con el estilo amigable --}}
                  <input class="mr-2 mt-1 rounded-md border-gray-300 text-primary focus:ring-primary" id="terms" name="terms" required type="checkbox">
                  <label class="text-sm text-gray-700" for="terms">
                    Acepto los <a class="text-primary font-semibold hover:underline" href="#">términos y
                      condiciones</a> y la <a class="text-primary font-semibold hover:underline" href="#">política
                      de privacidad</a>
                  </label>
                </div>
              </div>

              <div class="mb-2">
                <button class="btn-primary flex w-full items-center justify-center" type="submit">
                  <svg class="mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6z" />
                  </svg>
                  {{ __('Crear Cuenta') }}
                </button>
              </div>

              <div class="mt-4 text-center">
                <p class="text-gray-600">¿Ya tienes una cuenta? <a class="text-primary font-semibold hover:underline"
                    href="{{ route('login') }}">Inicia sesión</a></p>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
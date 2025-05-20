@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8">
  <div class="flex justify-center">
    <div class="w-full md:w-2/3 lg:w-1/2 xl:w-2/5">
      <div class="card float-animation">
        <div class="card-header flex items-center">
          <span class="pet-icon cat mr-2"></span>
          {{ __('Inicia Sesión') }}
        </div>

        <div class="card-body">
          <div class="mb-6 text-center">
            {{-- Se agregó un toque de color terciario y un pequeño shadow-sm al título para un estilo más amigable --}}
            <h2 class="text-2xl font-bold text-tertiary shadow-sm-text">¡Bienvenido a la comunidad PetShop!</h2>
            <p class="text-gray-500">Accede a tu cuenta para ver nuestras novedades</p>
          </div>

          <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-6">
              <label for="email" class="block mb-2 font-semibold text-gray-700">{{ __('Correo Electrónico') }}</label>
              <div>
                {{-- Se agregó la clase shadow-sm aquí --}}
                <input id="email" type="email" class="form-control w-full shadow-sm @error('email') is-invalid @enderror"
                  name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                  placeholder="tu@email.com" />
                @error('email')
                <span class="invalid-feedback block mt-1" role="alert">
                  <strong>{{ $message }}</strong>
                </span>
                @enderror
              </div>
            </div>

            <div class="mb-6">
              <label for="password" class="block mb-2 font-semibold text-gray-700">{{ __('Contraseña') }}</label>
              <div>
                {{-- Se agregó la clase shadow-sm aquí --}}
                <input id="password" type="password" class="form-control w-full shadow-sm @error('password') is-invalid @enderror"
                  name="password" required autocomplete="current-password" placeholder="Tu contraseña" />
                @error('password')
                <span class="invalid-feedback block mt-1" role="alert">
                  <strong>{{ $message }}</strong>
                </span>
                @enderror
              </div>
            </div>

            <div class="mb-6">
              <div class="flex items-center">
                {{-- Un pequeño ajuste de estilo al checkbox para que sea más coherente con el estilo amigable --}}
                <input type="checkbox" id="remember" name="remember" class="mr-2 rounded-md border-gray-300 text-primary focus:ring-primary" {{ old('remember') ? 'checked' : '' }}>
                <label for="remember" class="text-gray-700">Recordar mis datos</label>
              </div>
            </div>

            <div class="mb-2">
              <button type="submit" class="btn-primary w-full flex justify-center items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd"
                    d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z"
                    clip-rule="evenodd" />
                </svg>
                {{ __('Ingresar') }}
              </button>
            </div>

            <div class="text-center mt-4">
              <p class="text-gray-600">¿No tienes cuenta? <a href="{{ route('register') }}"
                  class="text-primary font-semibold hover:underline">Regístrate aquí</a></p>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
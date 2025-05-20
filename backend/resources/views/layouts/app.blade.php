<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ config('app.name', 'PetShop') }}</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">

  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

{{-- Se añadió 'flex flex-col' para permitir que el footer se fije abajo y el contenido principal ocupe el espacio --}}
<body class="font-sans antialiased flex flex-col min-h-screen"> 
  {{-- Se movió 'paw-print-bg' al body directamente para que cubra toda la página --}}
  <div class="paw-print-bg"></div> 
  
  {{-- Navbar con pequeños ajustes para la sombra del texto --}}
  <nav>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between h-16">
        <div class="flex">
          <div class="shrink-0 flex items-center">
            <a href="{{ route('dashboard') }}" class="flex items-center">
              <span class="pet-icon dog"></span>
              {{-- Se agregó text-shadow-sm para un efecto sutil --}}
              <span class="font-bold text-xl text-shadow-sm">PetShop</span> 
            </a>
          </div>
        </div>

        <div class="hidden sm:flex sm:items-center sm:ml-6">
          @auth
          <div class="ml-3 relative">
            <div>
              <button
                class="flex items-center text-sm font-medium focus:outline-none transition duration-150 ease-in-out">
                <div class="mr-2 text-shadow-sm">{{ Auth::user()->name }}</div> {{-- text-shadow-sm aquí también --}}

                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                  <path fill-rule="evenodd"
                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                    clip-rule="evenodd" />
                </svg>
              </button>
            </div>
          </div>
          @else
          <div class="flex space-x-4">
            <a href="{{ route('login') }}" class="font-medium text-shadow-sm">Ingresar</a> {{-- text-shadow-sm aquí --}}
            <a href="{{ route('register') }}" class="font-medium text-shadow-sm">Registro</a> {{-- text-shadow-sm aquí --}}
          </div>
          @endauth
        </div>
      </div>
    </div>
  </nav>

  {{-- Se añadió 'flex-grow' para que el main ocupe el espacio disponible y empuje el footer hacia abajo --}}
  <main class="py-8 flex-grow flex items-center justify-center"> 
    @yield('content')
  </main>

  <footer>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8 py-4">
        <div>
          <h3 class="font-bold text-lg mb-4">PetShop</h3>
          <p class="text-sm">Tienda especializada en productos para mascotas, cuidando a tus compañeros desde 2020.
          </p>
          <div class="flex items-center mt-4">
            <span class="pet-icon dog mr-2"></span>
            <span class="pet-icon cat mr-2"></span>
            <span class="pet-icon fish mr-2"></span>
          </div>
        </div>

        <div>
          <h3 class="font-bold text-lg mb-4">Enlaces</h3>
          <ul class="space-y-2 text-sm">
            <li><a href="#">Productos</a></li>
            <li><a href="#">Servicios</a></li>
            <li><a href="#">Nosotros</a></li>
            <li><a href="#">Contacto</a></li>
          </ul>
        </div>

        <div>
          <h3 class="font-bold text-lg mb-4">Contacto</h3>
          <address class="text-sm not-italic">
            <p>Av. Mascotas Felices 123</p>
            <p>Ciudad Mascota, CP 12345</p>
            <p class="mt-2">Email: info@petshop.com</p>
            <p>Tel: (123) 456-7890</p>
          </address>
        </div>
      </div>

      <div class="footer-divider"></div>

      <div class="py-4 text-sm text-center">
        &copy; {{ date('Y') }} PetShop. Todos los derechos reservados.
      </div>
    </div>
  </footer>
</body>

</html>
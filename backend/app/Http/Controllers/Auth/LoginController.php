<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
  /**
   * Mostrar formulario de registro
   */
  public function create(): View
  {
    return view('auth.login');
  }

  /**
   * Manejar solicitud de login
   */
  public function store(LoginRequest $request): RedirectResponse
  {
    // Los datos son validados por LoginRequest
    $request->authenticate();
    $request->session()->regenerate();

    return redirect()->route('dashboard')->with('success', '¡Login exitoso!');
  }

  /**
   * Cierra la sesión de usuario
   */
  public function destroy(Request $request): RedirectResponse
  {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/');
  }
}

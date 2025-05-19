<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class RegisterController extends Controller
{
    /**
     * Mostrar formulario de registro
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Manejar solicitud de registro
     */
    public function store(RegisterRequest $request): RedirectResponse
    {
        // Los datos ya están validados por RegisterRequest
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'birthdate' => $request->birthdate,
        ]);

        // Disparar evento de registro
        event(new Registered($user));

        // Iniciar sesión automáticamente
        Auth::login($user);

        // Crear token de Sanctum si se necesita API
        // $token = $user->createToken('auth_token')->plainTextToken;

        // Redireccionar al dashboard o home
        return redirect()->route('dashboard')->with('success', '¡Registro exitoso!');
    }
}

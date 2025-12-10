<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Configuracion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'password' => [
                'required',
                'min:8',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*#?&]/',
            ],
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Configuracion::create([
            'user_id'                    => $user->id,
            'max_porcentaje_gasto_total' => 30.00,
            'max_incremento_mensual'     => 20.00,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'estatus' => 1,
            'mensaje' => 'Usuario registrado correctamente',
            'user'    => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
            ],
            'access_token' => $token,
        ], 201);
    }


    public function login(Request $request)
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'estatus' => 0,
                'mensaje' => 'Credenciales incorrectas',
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'estatus'      => 1,
            'mensaje'      => 'Acceso correcto',
            'user'         => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
            ],
            'access_token' => $token,
        ]);
    }

    public function updatePassword(Request $request, $id)
    {
        $request->validate([
            'password' => ['required', 'min:8'],
        ]);

        $user = User::findOrFail($id);

        if ($request->user()->id !== $user->id) {
            return response()->json([
                'estatus' => 0,
                'mensaje' => 'No autorizado',
            ], 403);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'estatus' => 1,
            'mensaje' => 'Contraseña actualizada',
        ]);
    }

    public function userProfile(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'estatus' => 1,
            'mensaje' => 'Perfil de usuario',
            'user'    => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
            ],
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'estatus' => 1,
            'mensaje' => 'Cierre de sesion correcto',
        ]);
    }
}

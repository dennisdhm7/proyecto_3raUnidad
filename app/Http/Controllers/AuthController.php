<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Autenticar a un usuario o un cliente
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function authenticate(Request $request)
    {
        //Valida que el email y la contraseña se envíen desde el frontend
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);
        //Tipo de usuario autenticado
        $type = '';
        //Verificar si el usuario es un administrador
        $user = User::where('email', $request->email)->first();
        if (!$user || ($request->has('form') && $request->form == 'client')) {

            $user = Client::where('email', $request->email)->first();
            if (!$user) {
                throw ValidationException::withMessages([
                    'email' => ['No se encontró ningún usuario registrado con este correo.'],
                ]);
            } else {
                $type = 'client';
            }
        } else {
            //Establecer el tipo de usuario autenticado como administrador
            $type = 'user';
        }

        //Verificar si la contraseña es correcta
        if (!Hash::check($request->password, $user->password)) {
            //Retornar error de credenciales incorrectas
            throw ValidationException::withMessages([
                'password' => ['Las credenciales son incorrectas.'],
            ]);
        }
        if (!$user->active) {
            throw ValidationException::withMessages([
                'email' => ['Su cuenta ha sido bloqueada. Póngase en contacto con el administrador'],
            ]);
        }
        $user->update(['last_access' => Carbon::now()]);
        //Retornar los datos y el token
        $token = $user->createToken('f8b7c4a2e6d9c0a1b3f5')->plainTextToken;
        Notification::create([
            'content' => "{$user->name} se ha logueado en el sistema",
            'user_id' => null,
        ]);
        return response()->json([
            'user' => $user,
            'token' => $token,
            'type' => $type,
        ]);
    }

    /**
     * Cerrar la sesión del usuario logueado
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response('');
    }
}

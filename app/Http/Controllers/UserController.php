<?php

namespace App\Http\Controllers;

use App\Mail\RegistrationMail;
use App\Mail\RestorePasswordMail;
use App\Models\Business;
use App\Models\Item;
use App\Models\Link;
use App\Models\Notification;
use App\Models\User;
use App\Models\UserPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;
use Mail;

class UserController extends Controller
{
    /**
     * Retornar todos los usuarios registrados
     */
    public function index(Request $request)
    {
        if (!$request->user()->isAuthorized('Ver usuarios')) {
            return response()->json([]);
        }
        return response()->json(User::all());
    }

    /**
     * Obtener todos los usuarios activos de la base de datos
     */
    public function active(Request $request)
    {
        return response()->json(User::where('active', true)->get());
    }

    /**
     * Crear un nuevo usuario.
     */
    public function store(Request $request)
    {
        //Comprobar que el usuario sea un administrador o tenga permisos para esta acción
        if (!$request->user()->isAuthorized('Agregar usuarios')) {
            return response('Unauthorized', 401);
        }

        // Definir reglas de validación
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:Administrator,Manager,Waiter,Grocer,Other'
        ]);


        // Crear nuevo usuario
        try {
            $user = User::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'password' => bcrypt($request->input('password')),
                'phone' => $request->input('phone'),
                'active' => true,
                'role' => $request->role
            ]);

            if ($request->has('permissions')) {
                foreach ($request->permissions as $permission) {
                    UserPermission::create(['user_id' => $user->id, 'permission_id' => $permission]);
                }
            }

            $user->refresh();

            Notification::create([
                'content' => 'Ha registrado un nuevo usuario',
                'user_id' => $request->user()->id,
            ]);

            return response()->json($user, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al crear el nuevo usuario', 'exception' => $e->getMessage()], 500);
        }
    }

    /**
     * Actualizar un usuario existente.
     */
    public function update(Request $request, User $user)
    {
        //Comprobar que el usuario sea un administrador o que sea el usuario logueado el que está actualizando su perfil

        if ($request->user()->isAuthorized('Editar usuarios') || ($request->user()->getMorphClass() == 'App\\Models\\User' && $request->user()->id == $user->id)) {
            // Definir reglas de validación
            $data = $request->validate([
                'image' => 'filled|image',
                'name' => 'required|string|max:255',
                'phone' => 'required|string|max:15',
                'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
                'role' => 'required|in:Administrator,Manager,Waiter,Grocer,Other'

            ]);

            //Guardar imagen de perfil si se ha enviado
            if ($request->has('image')) {
                $image = $request->file('image')->store('users', 'src');
                $data['image'] = "src/$image";
            }

            UserPermission::where('user_id', $user->id)->delete();
            if ($request->has('permissions') && $request->permissions != '') {
                $permissions = explode(',', $request->permissions);
                foreach ($permissions as $permission) {
                    UserPermission::create(['user_id' => $user->id, 'permission_id' => $permission]);
                }
            }

            //Actualizar el usuario existente
            try {
                $user->update($data);

                Notification::create([
                    'content' => 'Ha actualizado los datos de un usuario',
                    'user_id' => $request->user()->id,
                ]);

                return response()->json($user);
            } catch (\Exception $e) {
                return response()->json(['error' => 'Error al actualizar el usuario', 'exception' => $e->getMessage()], 500);
            }
        } else {
            return response('Unauthorized', 401);
        }
    }

    /**
     * Actualizar el estado de un usuario existente.
     */
    public function changeStatus(Request $request, User $user)
    {
        //Comprobar que el usuario sea un administrador o tenga permisos para esta acción
        if (!$request->user()->isAuthorized('Editar usuarios')) {
            return response('Unauthorized', 401);
        }

        // Definir reglas de validación
        $data = $request->validate([
            'active' => 'required|boolean',
        ]);

        //Actualizar el estado del usuario existente
        try {
            $user->update($data);

            Notification::create([
                'content' => 'Ha modificado el estado de un usuario',
                'user_id' => $request->user()->id,
            ]);

            return response()->json($user);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al actualizar el estado del usuario', 'exception' => $e->getMessage()], 500);
        }
    }

    /**
     * Eliminar usuario existente de la base de datos
     */
    public function destroy(Request $request, User $user)
    {
        //Comprobar que el usuario sea un administrador o tenga permisos para esta acción
        if (!$request->user()->isAuthorized('Eliminar usuarios')) {
            return response('Unauthorized', 401);
        }

        $user->delete();

        Notification::create([
            'content' => 'Ha eliminado un usuario',
            'user_id' => $request->user()->id,
        ]);

        return response()->json(['result' => 'Usuario eliminado correctamente']);
    }
}

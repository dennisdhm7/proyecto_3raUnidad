<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use App\Models\Notification;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TableController extends Controller
{
    /**
     * Obtener todas las mesas de la base de datos
     */
    public function index(Request $request)
    {
        if (!$request->user()->isAuthorized('Ver mesas')) {
            return response()->json([]);
        }
        return response()->json(Table::all());
    }

    /**
     * Crear una nueva mesa
     */
    public function store(Request $request)
    {
        //Comprobar que el usuario sea un administrador o tenga permisos para esta acción
        if (!$request->user()->isAuthorized('Agregar mesas')) {
            return response('Unauthorized', 401);
        }

        //Definir reglas de validación
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'capacity' => 'required|integer',
        ]);

        //Crear nueva mesa
        try {
            $table = Table::create($data);

            Notification::create([
                'content' => 'Ha registrado una nueva mesa',
                'user_id' => $request->user()->id,
            ]);

            return response()->json($table, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al crear la nueva mesa', 'exception' => $e->getMessage()], 500);
        }
    }

    /**
     * Actualizar una mesa existente en la base de datos
     */
    public function update(Request $request, Table $table)
    {
        //Comprobar que el usuario sea un administrador o tenga permisos para esta acción
        if (!$request->user()->isAuthorized('Editar mesas')) {
            return response('Unauthorized', 401);
        }

        //Definir reglas de validación
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'capacity' => 'required|integer',
        ]);

        //Actualizar la mesa
        try {
            $table->update($data);

            Notification::create([
                'content' => 'Ha actualizado los datos de una mesa',
                'user_id' => $request->user()->id,
            ]);

            return response()->json($table);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al actualizar la mesa existente', 'exception' => $e->getMessage()], 500);
        }
    }

    /**
     * Eliminar la mesa existente en la base de datos.
     */
    public function destroy(Request $request, Table $table)
    {
        //Eliminar mesa
        $table->delete();

        Notification::create([
            'content' => 'Ha eliminado una mesa',
            'user_id' => $request->user()->id,
        ]);
        return response()->json(['result' => 'Mesa eliminada correctamente']);
    }
}

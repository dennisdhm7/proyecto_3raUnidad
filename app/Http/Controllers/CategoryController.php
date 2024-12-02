<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CategoryController extends Controller
{
    /**
     * Obtener todas las categorías de la base de datos
     */
    public function index(Request $request)
    {
        if (!$request->user()->isAuthorized('Ver categorías')) {
            return response()->json([]);
        }
        return response()->json(Category::all());
    }

    /**
     * Obtener todas las categorías activas de la base de datos
     */
    public function active(Request $request)
    {
        return response()->json(Category::where('active', true)->get());
    }

    /**
     * Obtener todos los productos de una categoría de la base de datos
     */
    public function items(Category $category)
    {
        return response()->json($category->items);
    }

    /**
     * Crear una nueva categoría
     */
    public function store(Request $request)
    {
        //Comprobar que el usuario sea un administrador o tenga permisos para esta acción
        if (!$request->user()->isAuthorized('Agregar categorías')) {
            return response('Unauthorized', 401);
        }

        //Definir reglas de validación
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'required|string|max:255',
            'active' => 'filled|boolean',
        ]);

        $data['active'] = true;

        //Crear nueva categoría
        try {
            $category = Category::create($data);

            Notification::create([
                'content' => 'Ha registrado una nueva categoría',
                'user_id' => $request->user()->id,
            ]);

            return response()->json($category, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al crear la nueva categoría', 'exception' => $e->getMessage()], 500);
        }
    }

    /**
     * Actualizar una categoría existente en la base de datos
     */
    public function update(Request $request, Category $category)
    {
        //Comprobar que el usuario sea un administrador o tenga permisos para esta acción
        if (!$request->user()->isAuthorized('Editar categorías')) {
            return response('Unauthorized', 401);
        }

        //Definir reglas de validación
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'required|string|max:255',
            'active' => 'filled|boolean',
        ]);

        //Actualizar la categoría
        try {
            $category->update($data);

            Notification::create([
                'content' => 'Ha actualizado los datos de una categoría',
                'user_id' => $request->user()->id,
            ]);

            return response()->json($category);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al actualizar la categoría existente', 'exception' => $e->getMessage()], 500);
        }
    }

    /**
     * Actualizar el estado de una categoría existente.
     */
    public function changeStatus(Request $request, Category $category)
    {
        //Comprobar que el usuario sea un administrador o tenga permisos para esta acción
        if (!$request->user()->isAuthorized('Editar categorías')) {
            return response('Unauthorized', 401);
        }

        // Definir reglas de validación
        $data = $request->validate([
            'active' => 'required|boolean',
        ]);

        //Actualizar el estado del producto existente
        try {
            $category->update($data);

            Notification::create([
                'content' => 'Ha modificado el estado de una categoría',
                'user_id' => $request->user()->id,
            ]);

            return response()->json($category);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al actualizar el estado de la categoría', 'exception' => $e->getMessage()], 500);
        }
    }

    /**
     * Eliminar la categoría existente en la base de datos.
     */
    public function destroy(Request $request, Category $category)
    {
        //Comprobar que el usuario sea un administrador o tenga permisos para esta acción
        if (!$request->user()->isAuthorized('Eliminar categorías')) {
            return response('Unauthorized', 401);
        }

        // Comprobar si la categoría está vinculada a algún producto
        $item = Item::where('category_id', $category->id)->first();
        if ($item) {
            throw ValidationException::withMessages([
                'name' => ['No se puede eliminar esta categoría porque está asociada a algunos productos.'],
            ]);
        } else {
            //Eliminar categoría
            $category->delete();

            Notification::create([
                'content' => 'Ha eliminado una categoría',
                'user_id' => $request->user()->id,
            ]);
            return response()->json(['result' => 'Categoría eliminada correctamente']);
        }
    }
}

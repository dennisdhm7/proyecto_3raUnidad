<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemVariation;
use App\Models\Notification;
use Buglinjo\LaravelWebp\Webp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    /**
     * Obtener todos los productos de la base de datos
     */
    public function index(Request $request)
    {
        if (!$request->user()->isAuthorized('Ver productos o servicios')) {
            return response()->json([]);
        }
        return response()->json(Item::all());
    }

    /**
     * Obtener todos los productos activos de la base de datos
     */
    public function active()
    {
        return response()->json(Item::where('active', true)->get());
    }

    /**
     * Obtener todos los productos activos de la base de datos
     */
    public function public()
    {
        $items = Item::where('active', true)->get()->groupBy(function ($item) {
            return $item->category->name;
        });
        return response()->json($items, 200, [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
    }

    /**
     * Obtener todos los datos de un producto
     */
    public function show(Item $item)
    {
        return response()->json($item);
    }

    /**
     * Obtener los datos de un producto determinado
     */
    public function random(string $ids)
    {
        $ids = explode('-', $ids);

        // Realiza la consulta
        $items = Item::whereIn('id', $ids)
            ->where('active', true)
            ->get();

        //Si no se obtuvieron todos los items rellenar con items aleatorios
        $count = $items->count();
        for ($i = $count; $i < count($ids); $i++) {
            $items[] = Item::where('active', true)->inRandomOrder()->take(1)->first();
        }

        //Obtener los items con los negocios
        $data = [];
        foreach ($items as $item) {
            $data[] = $item->toArrayWithBusiness();
        }

        return response()->json($data, 200);
    }

    /**
     * Crear un nuevo producto en la base de datos
     */
    public function store(Request $request)
    {
        //Comprobar que el usuario sea un administrador o tenga permisos para esta acción
        if (!$request->user()->isAuthorized('Agregar productos')) {
            return response('Unauthorized', 401);
        }

        // Definir reglas de validación
        $data = $request->validate([
            'image' => 'filled|string',
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string',
            'delivery' => 'filled|boolean',
            'vegan' => 'filled|boolean',
            'local_price' => 'required|numeric',
            'active' => 'filled|boolean',
        ]);

        if ($request->has('image')) {
            $image = $request->input('image');
            $image = str_replace('data:image/png;base64,', '', $image);
            $image = str_replace(' ', '+', $image);

            $name = Item::getSeoURL($request->name) . "-" . random_int(0, 99999);

            $aux = "{$name}.png";
            Storage::disk('src')->put("items/$aux", base64_decode($image));
            $data['aux_image'] = "src/items/$aux";

            $main = "{$name}.webp";
            Storage::disk('src')->put("items/$main", base64_decode($image));
            $data['image'] = "src/items/$main";
        }

        //Establecer los campos booleanos (si se mandan se traduce como true y si no se envían se traduce como false)
        $data['vegan'] = $request->has('vegan');
        $data['delivery'] = $request->has('delivery');

        // Crear nuevo producto
        try {
            $item = Item::create($data);

            if ($request->has('variations')) {
                //Guardar las variaciones del producto
                foreach ($request->variations as $variation) {
                    if ($variation['type'] != '') {
                        foreach ($variation['name'] as $name) {
                            ItemVariation::create([
                                'item_id' => $item->id,
                                'type' => $variation['type'],
                                'name' => $name,
                            ]);
                        }
                    }
                }
            }

            Notification::create([
                'content' => 'Ha registrado un nuevo producto',
                'user_id' => $request->user()->id,
            ]);

            return response()->json($item, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al crear el nuevo producto', 'exception' => $e->getMessage()], 500);
        }
    }

    /**
     * Actualizar un producto existente en la base de datos
     */
    public function update(Request $request, Item $item)
    {
        //Comprobar que el usuario sea un administrador o tenga permisos para esta acción
        if (!$request->user()->isAuthorized('Editar productos')) {
            return response('Unauthorized', 401);
        }

        // Definir reglas de validación
        $data = $request->validate([
            'image' => 'filled|image',
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string',
            'delivery' => 'filled|boolean',
            'vegan' => 'filled|boolean',
            'local_price' => 'required|numeric',
            'active' => 'filled|boolean',
        ]);

        $old_image = null;

        //Guardar imagen del producto si se enviaron nuevas imágenes
        if ($request->has('image')) {
            $file = $request->file('image');
            //Inicializar conversión a webp
            $webp = Webp::make($request->file('image'));
            //Extensión original
            $extension = $file->getClientOriginalExtension();
            //Nombre del archivo
            $name = Item::getSeoURL($request->name) . "-" . random_int(0, 99999);
            //Guardar archivo original
            $path = $file->storeAs('items', "$name.$extension", 'src');
            $data['aux_image'] = "src/$path";

            if ($webp->save(Storage::path("items/$name.webp"))) {
                $data['image'] = "src/items/$name.webp";
            }

            $old_image = [$item->image, $item->aux_image];
        }

        //Establecer los campos booleanos (si se mandan se traduce como true y si no se envían se traduce como false)
        $data['vegan'] = $request->has('vegan');
        $data['delivery'] = $request->has('delivery');

        // Actualizar el producto existente
        try {
            $item->update($data);

            ItemVariation::where('item_id', $item->id)->delete();
            if ($request->has('variations')) {
                //Guardar las variaciones del producto
                foreach ($request->variations as $variation) {
                    if ($variation['type'] != '') {
                        foreach ($variation['name'] as $name) {
                            ItemVariation::create([
                                'item_id' => $item->id,
                                'type' => $variation['type'],
                                'name' => $name,
                            ]);
                        }
                    }
                }
            }

            //Eliminar imagen vieja
            if ($old_image != null) {
                if ($old_image[0] != null) {
                    $image = substr($old_image[0], 4);
                    Storage::delete($image);
                }
                if ($old_image[1] != null) {
                    $aux = substr($old_image[1], 4);
                    Storage::delete($aux);
                }

            }
            Notification::create([
                'content' => 'Ha actualizado los datos de un producto',
                'user_id' => $request->user()->id,
            ]);

            return response()->json($item);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al actualizar el nuevo producto', 'exception' => $e->getMessage()], 500);
        }
    }

    /**
     * Actualizar el estado de un producto existente.
     */
    public function changeStatus(Request $request, Item $item)
    {
        //Comprobar que el usuario sea un administrador o tenga permisos para esta acción
        if (!$request->user()->isAuthorized('Editar productos')) {
            return response('Unauthorized', 401);
        }

        // Definir reglas de validación
        $data = $request->validate([
            'active' => 'required|boolean',
        ]);

        //Actualizar el estado del producto existente
        try {
            $item->update($data);

            Notification::create([
                'content' => 'Ha modificado el estado de un producto',
                'user_id' => $request->user()->id,
            ]);

            return response()->json($item);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al actualizar el estado del usuario', 'exception' => $e->getMessage()], 500);
        }
    }

    /**
     * Eliminar un producto existente en la base de datos
     */
    public function destroy(Request $request, Item $item)
    {
        //Comprobar que el usuario sea un administrador o tenga permisos para esta acción
        if (!$request->user()->isAuthorized('Eliminar productos')) {
            return response('Unauthorized', 401);
        }

        Notification::create([
            'content' => 'Ha eliminado un producto',
            'user_id' => $request->user()->id,
        ]);

        $item->delete();
        return response()->json(['result' => 'Producto eliminado correctamente']);
    }
}

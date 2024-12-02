<?php

namespace App\Http\Controllers;

use App\Mail\NotificationMail;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Mail;

class OrderController extends Controller
{
    /**
     * Obtener todas las órdenes de la base de datos
     */
    public function index(Request $request)
    {
        if (!$request->user()->isAuthorized('Ver pedidos')) {
            return response()->json([]);
        }

        //Obtener el número de elementos por página
        $per_page = $request->get('per_page', 15);
        $search = $request->get('search');
        $sort_key = $request->get('sort_key');
        $sort_order = $request->get('sort_order');

        if ($search == '') {
            if ($sort_key == '') {
                //Obtener los datos de la página
                $orders = Order::latest()->paginate($per_page);
            } else {
                //Obtener los datos de la página ordenados
                $orders = Order::orderBy($sort_key, $sort_order)->paginate($per_page);
            }
        } else {
            $filter = Order::where(function ($query) use ($search) {
                //Añadir las condiciones de búsqueda para las columnas de orders
                $query->where('transaction_id', 'like', "%$search%")
                    ->orWhere('paid_at', 'like', "%$search%");
            })->orWhereHas('user', function ($query) use ($search) {
                //Añadir las condiciones de búsqueda para los campos de clients
                $query->where('name', 'like', "%$search%")
                    ->orWhere('phone', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%");
            });

            if ($sort_key == '') {
                //Obtener los datos de la página
                $orders = $filter->latest()->paginate($per_page);
            } else {
                $orders = $filter::latest()->orderBy($sort_key, $sort_order)->paginate($per_page);
            }
        }

        //Devolver la respuesta
        return response()->json($orders);
    }

    /**
     * Obtener todas las órdenes de la base de datos
     */
    public function client(Request $request)
    {
        if (!$request->user()->isAuthorized('Ver pedidos')) {
            return response()->json([]);
        }

        //Obtener el número de elementos por página
        $per_page = $request->get('per_page', 15);
        $search = $request->get('search');
        $sort_key = $request->get('sort_key');
        $sort_order = $request->get('sort_order');

        if ($search == '') {
            if ($sort_key == '') {
                //Obtener los datos de la página
                $orders = Order::where('client_id', $request->user()->id)->latest()->paginate($per_page);
            } else {
                //Obtener los datos de la página ordenados
                $orders = Order::where('client_id', $request->user()->id)->orderBy($sort_key, $sort_order)->paginate($per_page);
            }
        } else {
            $filter = Order::where('client_id', $request->user()->id)->where(function ($query) use ($search) {
                //Añadir las condiciones de búsqueda para las columnas de orders
                $query->where('transaction_id', 'like', "%$search%")
                    ->orWhere('paid_at', 'like', "%$search%");
            })->orWhereHas('user', function ($query) use ($search) {
                //Añadir las condiciones de búsqueda para los campos de clients
                $query->where('name', 'like', "%$search%")
                    ->orWhere('phone', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%");
            });

            if ($sort_key == '') {
                //Obtener los datos de la página
                $orders = $filter->latest()->paginate($per_page);
            } else {
                $orders = $filter::latest()->orderBy($sort_key, $sort_order)->paginate($per_page);
            }
        }

        //Devolver la respuesta
        return response()->json($orders);
    }

    /**
     * Crear un nuevo pedido
     */
    public function store(Request $request)
    {
        //Comprobar que el usuario sea un administrador o tenga permisos para esta acción
        if (!$request->user()->isAuthorized('Agregar pedidos')) {
            return response('Unauthorized', 401);
        }

        //Definir reglas de validación
        $data = $request->validate([
            // 'user_id' => 'nullable|exists:users,id',
            'type' => 'required|in:Delivery,Local,Sale',
            'payment_method' => 'required|in:Cash,Card',
            'total' => 'required|numeric',
            'client_id' => 'filled',
            'address' => 'filled|string',
            'client' => 'filled|string',
            'phone' => 'filled|string',
            'email' => 'filled|string',
        ]);

        $data['order_id'] = substr(Str::uuid(), 0, 8);
        if ($request->has('client_id')) {

        } else {
            $data['user_id'] = $request->user()->id;
        }
        if ($request->type != 'Delivery' || $request->payment_method == 'Card') {
            $data['status'] = 'Paid';
        }

        //Crear nuevo pedido
        try {
            $total = 0;
            foreach ($request->items as $item) {
                $total += $item['price'] * $item['quantity'];
            }
            if ($request->total < $total) {
                throw ValidationException::withMessages([
                    'total' => ['El total pagado es menor que el monto total del pedido'],
                ]);
            }

            $data['rest'] = $request->total - $total;
            $order = Order::create($data);

            foreach ($request->items as $item) {
                //Crear item de pedido
                OrderItem::create([
                    'order_id' => $order->id,
                    'item_id' => $item['item_id'],
                    'item_variation_id' => isset($item['item_variation_id']) ? $item['item_variation_id'] : null,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'notes' => $item['notes'],
                ]);
            }

            Notification::create([
                'content' => 'Ha registrado un nuevo pedido',
                'user_id' => null,
            ]);

            return response()->json($order, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al crear el nuevo pedido', 'exception' => $e->getMessage()], 500);
        }
    }

    /**
     * Actualizar un pedido en la base de datos
     */
    public function update(Request $request, Order $order)
    {
        //Comprobar que el usuario sea un administrador o tenga permisos para esta acción
        if (!$request->user()->isAuthorized('Actualizar pedidos')) {
            return response('Unauthorized', 401);
        }

        //Definir reglas de validación
        $data = $request->validate([
            'payment_method' => 'required|in:Cash,Card',
            'total' => 'required|numeric',
        ]);

        $data['user_id'] = $request->user()->id;

        //Actualizar el pedido
        try {
            $total = 0;
            foreach ($request->items as $item) {
                $total += $item['price'] * $item['quantity'];
            }
            if ($request->total < $total) {
                throw ValidationException::withMessages([
                    'total' => ['El total pagado es menor que el monto total del pedido'],
                ]);
            }

            $data['rest'] = $request->total - $total;
            $order->update($data);

            OrderItem::where('order_id', $order->id)->delete();
            foreach ($request->items as $item) {
                //Crear item de pedido
                OrderItem::create([
                    'order_id' => $order->id,
                    'item_id' => $item['item_id'],
                    'item_variation_id' => $item['item_variation_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'notes' => $item['notes'],
                ]);
            }

            Notification::create([
                'content' => 'Ha actualizado el pedido: ' . $order->order_id,
                'user_id' => $request->user()->id,
            ]);

            return response()->json($order, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al actualizar los datos del pedido', 'exception' => $e->getMessage()], 500);
        }
    }

    /**
     * Actualizar el estado de un usuario existente.
     */
    public function changeStatus(Request $request, Order $order)
    {
        //Comprobar que el usuario sea un administrador o tenga permisos para esta acción
        if (!$request->user()->isAuthorized('Editar usuarios')) {
            return response('Unauthorized', 401);
        }

        // Definir reglas de validación
        $data = $request->validate([
            'status' => 'required|string',
        ]);

        //Actualizar el estado del usuario existente
        try {
            $order->update($data);

            Notification::create([
                'content' => 'Ha modificado el estado de un pedio',
                'user_id' => null,
            ]);

            if ($order->client_data != null) {
                Mail::to($order->client_data->email)->send(new NotificationMail('Ha cambiado el estado de su pedido', $order->client_data->name, 'El estado de su pedido ha cambiado, puede consultarlo en nuestra web'));
            }

            return response()->json($order);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al actualizar el estado del usuario', 'exception' => $e->getMessage()], 500);
        }
    }

    /**
     * Cancelar un pedido
     */
    public function destroy(Order $order)
    {
        $order->update(['status' => 'Cancelled']);
        return response()->json($order);
    }
}

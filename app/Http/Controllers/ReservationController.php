<?php

namespace App\Http\Controllers;

use App\Mail\NotificationMail;
use App\Models\Notification;
use App\Models\Reservation;
use App\Models\ReservationTable;
use App\Models\Table;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Log;
use Mail;

class ReservationController extends Controller
{
    /**
     * Obtener todas las reservaciones de la base de datos
     */
    public function index(Request $request)
    {
        if (!$request->user()->isAuthorized('Ver reservaciones')) {
            return response()->json([]);
        }
        return response()->json(Reservation::all());
    }

    /**
     * Obtener todas las reservaciones de la base de datos
     */
    public function client(Request $request)
    {
        if (!$request->user()->isAuthorized('Ver reservaciones')) {
            return response()->json([]);
        }
        return response()->json(Reservation::where('client_id', $request->user()->id)->get());
    }

    /**
     * Crear una nueva reservación
     */
    public function store(Request $request)
    {
        //Comprobar que el usuario sea un administrador o tenga permisos para esta acción
        if (!$request->user()->isAuthorized('Agregar reservaciones')) {
            return response('Unauthorized', 401);
        }

        //Definir reglas de validación
        $data = $request->validate([
            'client' => 'required|string|max:255',
            'client_quantity' => 'required|integer',
            'reservation_datetime' => 'required|date',
            'reservation_time' => 'required|integer',
            'tables' => 'required',
            'client_id' => 'filled'
        ]);

        $data['reservation_id'] = substr(Str::uuid(), 0, 8);
        unset($data['tables']);

        //Crear nueva reservación
        try {
            $reservation = Reservation::create($data);

            //Crear las tablas de la reservación
            foreach ($request->tables as $table) {
                ReservationTable::create([
                    'reservation_id' => $reservation->id,
                    'table_id' => $table,
                ]);
            }

            Notification::create([
                'content' => 'Ha registrado una nueva reservación',
                'user_id' => null,
            ]);

            return response()->json($reservation, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al crear la nueva reservación', 'exception' => $e->getMessage()], 500);
        }
    }

    /**
     * Actualizar una reservación existente en la base de datos
     */
    public function update(Request $request, Reservation $reservation)
    {
        //Comprobar que el usuario sea un administrador o tenga permisos para esta acción
        if (!$request->user()->isAuthorized('Editar reservaciones')) {
            return response('Unauthorized', 401);
        }

        //Definir reglas de validación
        $data = $request->validate([
            'client' => 'required|string|max:255',
            'client_quantity' => 'required|integer',
            'reservation_datetime' => 'required|date',
            'reservation_time' => 'required|integer',
        ]);

        //Actualizar la reservación
        try {
            $reservation->update($data);

            Notification::create([
                'content' => 'Ha actualizado los datos de una reservación',
                'user_id' => $request->user()->id,
            ]);

            return response()->json($reservation);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al actualizar la reservación existente', 'exception' => $e->getMessage()], 500);
        }
    }

    /**
     * Obtiene las mesas disponibles para una reservación determinada
     */
    public function check(Request $request, $datetime, $time)
    {
        $tables = [];
        foreach (Table::all() as $table) {
            $tables[] = $table->id;
        }
        $datetime = str_replace('_', ' ', $datetime);

        $start = Carbon::parse($datetime);
        $end = Carbon::parse($datetime)->addMinutes(intval($time));
        $limit = Carbon::parse($datetime)->subHours(25);

        $reservations = Reservation::where('reservation_datetime', '>', $limit)->get();
        foreach ($reservations as $reservation) {
            $r_start = Carbon::parse($reservation->reservation_datetime);
            $r_end = Carbon::parse($reservation->reservation_datetime)->addMinutes(intval($reservation->reservation_time));

            if ($start->between($r_start, $r_end) || $end->between($r_start, $r_end)) {
                foreach ($reservation->tables as $table) {
                    // Buscar la clave de la mesa a eliminar
                    $i = array_search($table->id, $tables);
                    if ($i !== false) {
                        // Eliminar la mesa
                        unset($tables[$i]);
                    }
                }
            }
        }

        return response()->json(array_values($tables));
    }

    /**
     * Actualizar el estado de una reservación existente.
     */
    public function changeStatus(Request $request, Reservation $reservation)
    {
        //Comprobar que el usuario sea un administrador o tenga permisos para esta acción
        if (!$request->user()->isAuthorized('Editar reservaciones')) {
            return response('Unauthorized', 401);
        }

        // Definir reglas de validación
        $data = $request->validate([
            'status' => 'required|string',
        ]);

        //Actualizar el estado del producto existente
        try {
            $reservation->update($data);

            Notification::create([
                'content' => 'Ha modificado el estado de la reservación',
                'user_id' => $request->user()->id,
            ]);

            if ($reservation->client_data != null) {
                Mail::to($reservation->client_data->email)->send(new NotificationMail('Ha cambiado el estado de su reservación', $reservation->client_data->name, 'El estado de su reservación ha cambiado, puede consultarlo en nuestra web'));
            }

            return response()->json($reservation);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al actualizar el estado de la reservación', 'exception' => $e->getMessage()], 500);
        }
    }

    /**
     * Eliminar la reservación existente en la base de datos.
     */
    public function destroy(Request $request, Reservation $reservation)
    {
        //Eliminar reservación
        $reservation->update(['status' => 'Cancelled']);

        Notification::create([
            'content' => 'Ha cancelado una reservación',
            'user_id' => null,
        ]);
        return response()->json($reservation);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //Obtener el número de elementos por página
        $per_page = $request->get('per_page', 50);

        if ($request->user()->isAuthorized('Ver notificaciones')) {
            $notifications = Notification::latest()->paginate($per_page);
        } else {
            $notifications = Notification::latest()->paginate($per_page);
        }

        //Devolver la respuesta
        return response()->json($notifications);
    }
}

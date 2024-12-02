<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\UserController;
use App\Models\Order;
use App\Models\Permission;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('login', action: [AuthController::class, 'authenticate']);
Route::post('register', action: [ClientController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    //Endpoint para cerrar la sesión
    Route::post('/logout', [AuthController::class, 'logout']);

    //Obtener monedas
    Route::get('/currencies', [CurrencyController::class, 'index']);

    //Obtener categorías activas
    Route::get('/categories/active', [CategoryController::class, 'active']);
    //Endpoints de categorías
    Route::resource('categories', CategoryController::class);
    Route::put('/categories/status/{category}', [CategoryController::class, 'changeStatus']);

    //Endpoints de productos
    Route::get('/items/active', [ItemController::class, 'active']);
    Route::resource('items', ItemController::class);
    Route::put('/items/status/{item}', [ItemController::class, 'changeStatus']);

    //Endpoint para notificaciones
    Route::get('/notifications', [NotificationController::class, 'index']);

    //Obtener usuarios activos
    Route::get('/users/active', [UserController::class, 'active']);
    //Endpoints de usuarios
    Route::resource('users', UserController::class);
    Route::put('/users/status/{user}', [UserController::class, 'changeStatus']);

    //Endpoint para pedidos
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/client-orders', [OrderController::class, 'client']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::put('/orders/{order}', [OrderController::class, 'update']);
    Route::delete('/orders/{order}', [OrderController::class, 'destroy']);
    Route::put('/orders/status/{order}', [OrderController::class, 'changeStatus']);
    //Endpoints de mesas
    Route::resource('tables', TableController::class);

    //Endpoints de reservaciones
    Route::resource('reservations', ReservationController::class);
    Route::get('/client-reservations', [ReservationController::class, 'client']);
    Route::put('/reservations/status/{reservation}', [ReservationController::class, 'changeStatus']);
    Route::get('reservations/tables-check/{datetime}/{time}', [ReservationController::class, 'check']);

    //Endpoints de permisos
    Route::get('permissions', function () {
        return response()->json(Permission::all());
    });

});

Route::get('voucher/{order}', function (Order $order) {
    $total = 0;
    foreach($order->items as $item) {
        $total += $item->quantity * $item->price;
    }
    $days = array('Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado');
    $date = Carbon::now();
    $date = $days[date('w', strtotime($date))] . ", " . date('d/m/Y', strtotime($date));
    $dompdf = Pdf::loadView("voucher", [
        "order" => $order,
        "total" => $total,
        "print" => $days[date('w', strtotime(now()))] . ", " . date('d/m/Y', strtotime(now()))
    ]);

    return $dompdf->stream();
});
Route::get('/menu/items', [ItemController::class, 'public']);

<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    /**
     * Obtener todos los productos de la base de datos
     */
    public function index(Request $request)
    {
        return response()->json(Currency::all());
    }
}

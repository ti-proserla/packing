<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::resource('producto', 'ProductoController');
Route::resource('cliente', 'ClienteController');
Route::get('materia/variedad', 'MateriaController@variedad');
Route::resource('materia', 'MateriaController');
Route::resource('variedad', 'VariedadController');
Route::resource('transportista', 'TransportistaController');
Route::resource('lote_ingreso', 'LoteIngresoController');
Route::resource('sub_lote', 'SubLoteController');
Route::resource('palet_entrada', 'PaletEntradaController');
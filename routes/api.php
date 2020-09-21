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
Route::resource('palet_salida/{id}/jaba', 'JabaSalidaController');
Route::resource('palet_salida', 'PaletSalidaController');
Route::get('lote_ingreso/palets_salida', 'LoteIngresoController@palets_salida');
Route::resource('lote_ingreso', 'LoteIngresoController');
Route::get('lote_ingreso/{id}/sub_lote', 'SubLoteController@index');
Route::post('sub_lote', 'SubLoteController@store');
// Route::get('palet_entrada', 'PaletEntradaController');
Route::resource('sub_lote/{sub_lote_id}/palet_entrada', 'PaletEntradaController');
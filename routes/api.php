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
Route::get('materia/detallado', 'MateriaController@detallado');
Route::resource('impresora', 'ImpresoraController');
Route::resource('materia', 'MateriaController');
Route::resource('variedad', 'VariedadController');
Route::resource('calibre', 'CalibreController');
Route::resource('categoria', 'CategoriaController');
Route::resource('presentacion', 'PresentacionController');
Route::resource('plu', 'PLUController');
Route::resource('marca-caja', 'MarcaCajaController');
Route::resource('marca-empaque', 'MarcaEmpaqueController');
Route::resource('tipo-empaque', 'TipoEmpaqueController');
Route::resource('tipo', 'TipoController');
Route::post('operacion/addPalet', 'OperacionController@addPalet');
Route::resource('operacion', 'OperacionController');
Route::resource('etiqueta-caja', 'EtiquetaCajaController');

Route::get('fundo/detallado', 'FundoController@detallado');
Route::resource('fundo', 'FundoController');

Route::resource('transportista', 'TransportistaController');
Route::resource('palet_salida/{id}/jaba', 'JabaSalidaController');
Route::post('palet_salida/{id}/caja', 'PaletSalidaController@caja_store');
Route::resource('palet_salida', 'PaletSalidaController');
Route::get('lote_ingreso/generar_codigo', 'LoteIngresoController@generar_codigo');
Route::get('lote_ingreso/palets_salida', 'LoteIngresoController@palets_salida');
Route::resource('lote_ingreso', 'LoteIngresoController');
Route::get('sub_lote/{id}/palets', 'SubLoteController@palets');
Route::put('sub_lote/{sub_lote_id}/estado', 'SubLoteController@estado');
Route::get('sub_lote', 'SubLoteController@index');
Route::post('sub_lote', 'SubLoteController@store');
// Route::get('palet_entrada', 'PaletEntradaController');
Route::resource('sub_lote/{sub_lote_id}/palet_entrada', 'PaletEntradaController');
Route::get('lanzado','LanzadoController@index');
Route::patch('lanzado','LanzadoController@palet_entrada');
/**
 * REPORTES
 */
Route::get('rendimiento-personal', 'ReportesController@rendimiento_personal');
Route::get('cantidad-por-linea', 'ReportesController@cantidad_por_linea');
Route::get('reporte/lote', 'ReportesController@lote');
Route::get('reporte/acopio', 'ReportesController@acopio');
Route::get('reporte/avance_lote', 'ReportesController@avance_lote');
Route::get('reporte/avance_personal', 'ReportesController@avance_personal');
Route::get('print/muestra_etiqueta_caja', 'PrintZPLController@muestra_etiqueta_caja');
Route::get('print/cajas', 'PrintZPLController@caja_palet');
Route::get('print/zpl/cajas', 'PrintZPLController@cajas');
Route::get('print/zpl/palet_entrada', 'PrintZPLController@palet_entrada');
Route::get('print/zpl/palet_entrada/all', 'PrintZPLController@palet_entrada_all');

//Replicar Base de datos
Route::get('replicar/puerto_embarque','NisiraPuertoEmbarque@puerto_embarque');
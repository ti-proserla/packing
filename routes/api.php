<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

ini_set('memory_limit', '512M');

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
Route::resource('zpl', 'ZplController');
Route::resource('impresora', 'ImpresoraController');
Route::resource('materia', 'MateriaController');
Route::resource('campania', 'CampaniaController');
Route::resource('variedad', 'VariedadController');
Route::resource('parihuela', 'ParihuelaController');
Route::resource('calibre', 'CalibreController');
Route::resource('categoria', 'CategoriaController');
Route::resource('presentacion', 'PresentacionController');
Route::resource('plu', 'PLUController');
Route::resource('marca-caja', 'MarcaCajaController');
Route::resource('marca-empaque', 'MarcaEmpaqueController');
Route::resource('tipo-empaque', 'TipoEmpaqueController');
Route::resource('tipo', 'TipoController');
Route::resource('tipo-palet', 'TipoPaletController');
Route::resource('caja', 'CajaController');
Route::post('operacion/addPalet', 'OperacionController@addPalet');
Route::resource('operacion', 'OperacionController');
Route::resource('produccion', 'ProduccionController');
Route::resource('descarte', 'DescarteController');
Route::resource('etiqueta-caja', 'EtiquetaCajaController');
Route::resource('rendimiento-personal', 'RendimientoPersonalController');
Route::put('presentacion_linea/{id}', 'PresentacionLineaController@cerrar');
Route::resource('presentacion_linea', 'PresentacionLineaController');


Route::get('fundo/detallado', 'FundoController@detallado');
Route::resource('fundo', 'FundoController');

Route::resource('transportista', 'TransportistaController');
Route::resource('palet_salida/{id}/jaba', 'JabaSalidaController');
Route::get('palet_salida/search', 'PaletSalidaController@search');
Route::post('palet_salida/transferencia', 'PaletSalidaController@transferencia');
Route::post('palet_salida/remonte', 'PaletSalidaController@remonte');
Route::post('palet_salida/{id}/caja', 'PaletSalidaController@caja_store');
Route::resource('palet_salida', 'PaletSalidaController');
Route::get('lote_ingreso/generar_codigo', 'LoteIngresoController@generar_codigo');
Route::get('lote_ingreso/palets_salida', 'LoteIngresoController@palets_salida');
Route::post('lote_ingreso/{codigo}/codigo', 'LoteIngresoController@codigo');
Route::resource('lote_ingreso', 'LoteIngresoController');
Route::get('sub_lote/{id}/palets', 'SubLoteController@palets');
Route::put('sub_lote/{sub_lote_id}/estado', 'SubLoteController@estado');
Route::resource('sub_lote', 'SubLoteController');
// Route::get('palet_entrada', 'PaletEntradaController');
Route::resource('sub_lote/{sub_lote_id}/palet_entrada', 'PaletEntradaController');
Route::patch('palet_entrada/{id}', 'PaletEntradaController@update');
Route::get('lanzado','LanzadoController@index');
Route::patch('lanzado','LanzadoController@palet_entrada');
Route::patch('lanzado/{id}/cerrar','LanzadoController@cerrar');
/**
 * REPORTES
 */
Route::get('rendimiento-personal', 'ReportesController@rendimiento_personal');
Route::get('cantidad-por-linea', 'ReportesController@cantidad_por_linea');
Route::get('reporte/lote', 'ReportesController@lote');
Route::get('reporte/producto-terminado', 'ReportesController@producto_terminado');
Route::get('reporte/producto-terminado-linea', 'ReportesController@producto_terminado_linea');
Route::get('reporte/acopio', 'ReportesController@acopio');
Route::get('reporte/lanzado', 'ReportesController@lanzado');
Route::get('reporte/rendimiento_linea', 'ReportesController@rendimiento_linea');
Route::get('reporte/avance_lote', 'ReportesController@avance_lote');
Route::get('reporte/avance_personal', 'ReportesController@avance_personal');
Route::get('reporte/rendimiento_personal_presentacion', 'ReportesController@rendimiento_personal_presentacion');
Route::get('reporte/bono-personal', 'ReportesController@bono_personal');
Route::get('reporte/consolidado-bonos', 'ReportesController@consolidado_bonos');
Route::get('reporte/consumo-viaje', 'ReportesController@consumo_viaje');
Route::get('reporte/balance-materia', 'ReportesController@balance_materia');
Route::get('reporte/aforo', 'ReportesController@aforo');
Route::get('reporte/cantidad-labor', 'ReportesController@cantidad_labor');
Route::get('print/muestra_etiqueta_caja', 'PrintZPLController@muestra_etiqueta_caja');
Route::get('print/cajas', 'PrintZPLController@caja_palet');
Route::get('print/zpl/cajas', 'PrintZPLController@cajas');
Route::get('print/zpl/palet_salida', 'PrintZPLController@palet_salida');
Route::get('print/zpl/palet_entrada', 'PrintZPLController@palet_entrada');
Route::get('print/zpl/palet_entrada/all', 'PrintZPLController@palet_entrada_all');
Route::post('zpl/preview', 'PrintZPLController@preview');

//Replicar Base de datos
Route::get('conector/puerto-embarque','NisiraConector@puerto_embarque');

Route::get('prueba',function(){
    phpinfo();
});
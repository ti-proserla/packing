<?php

namespace App\Http\Controllers;

use App\Model\LoteIngreso;
use App\Model\PaletSalida;
use App\Model\JabaSalida;
use App\Model\RendimientoPersonal;
use App\Model\Caja;
use App\Model\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PaletSalidaController extends Controller
{
    public function index(Request $request)
    {
        if ($request->has('estado')) {
            $paletSalidas=PaletSalida::join('cliente','cliente.id','=','palet_salida.cliente_id')
                                ->leftJoin('caja','caja.palet_salida_id','=','palet_salida.id')
                                ->select('palet_salida.*','cliente.descripcion as cliente',DB::raw('COUNT(caja.id) cajas_contadas'))
                                ->groupBy('palet_salida.id')
                                ->whereIn('estado',explode(',',$request->estado))
                                ->get();
        }else{
            $paletSalidas=PaletSalida::join('cliente','cliente.id','=','palet_salida.cliente_id')
                                ->leftJoin('caja','caja.palet_salida_id','=','palet_salida.id')
                                ->select('palet_salida.*','cliente.descripcion as cliente',DB::raw('COUNT(caja.id) cajas_contadas'))
                                ->groupBy('palet_salida.id')
                                ->get();
        }
        return response()->json($paletSalidas);
    }  
    /**
     * La cantidad se Agrega por defecto 0 y va creciendo conforme se escanean Jabas.
     */
    public function store(Request $request)
    {
        $paletSalida=new PaletSalida();
        $paletSalida->cliente_id=$request->cliente_id;
        $paletSalida->etapas=$request->etapas;
        $paletSalida->nave=1;
        $paletSalida->camara=null;
        $paletSalida->estado="Pendiente";
        $paletSalida->save();
        return response()->json([
            "status" => "OK",
            "data"  => $paletSalida
        ]);
    }
    
    public function caja_store($id,Request $request){
        // dd($request->all());
        $codigo_palet = $request->codigo_palet;
        $array_palet= explode('-',$codigo_palet);
        
        $caja=new Caja();
        $caja->palet_salida_id=(int)$id;
        $caja->etiqueta_caja_id=$array_palet[1];
        $caja->save();

        foreach ($request->codigos_trabajador as $key => $codigo) {
            if (!strpos($codigo,"00000000")) {
                $rendimientoPersonal=new RendimientoPersonal();
                $rendimientoPersonal->caja_id=$caja->id;
                $rendimientoPersonal->codigo_barras=$codigo;
                $rendimientoPersonal->codigo_operador=substr($codigo,4,8);
                $rendimientoPersonal->linea=substr($codigo,0,2);
                $rendimientoPersonal->codigo_labor=substr($codigo,2,2);
                $rendimientoPersonal->save();
            }
        }

        return response()->json([
            "status" => "OK",
            "data"  => [
                "calibre" => $caja->calibre,
                "categoria" => $caja->categoria,
                "presentacion" => $caja->presentacion,
            ]
        ]);
        
        // $caja->
    }

    public function show(Request $request,$id){
        $paletSalida=PaletSalida::with('cajas')
                                ->where('palet_salida.id',$id)
                                ->select('palet_salida.*')
                                ->first();
        return response()->json($paletSalida);
    }

    public function update(Request $request,$id){
        switch ($request->estado) {
            case 'Cerrado':
                $paletSalida=PaletSalida::where('id',$id)->first();
                $cliente=Cliente::where('id',$paletSalida->cliente_id)->first();
                $cliente->conteo_palets=$cliente->conteo_palets+1;
                $cliente->save();
                $paletSalida->numero=$cliente->conteo_palets;
                $paletSalida->estado=$request->estado;
                $paletSalida->fecha_cierre=Carbon::now();
                $paletSalida->save();
                break;
            
            case 'Frio':
                $paletSalida=PaletSalida::where('id',$id)->first();
                $paletSalida->estado=$request->estado;
                $paletSalida->camara=$request->camara;
                $paletSalida->save();
                break;

            default:
                $paletSalida=PaletSalida::where('id',$id)->first();
                $paletSalida->estado=$request->estado;
                $paletSalida->save();
                break;
        }
        return response()->json([
            "status" => "OK",
            "data"  => $paletSalida
        ]);
    }

    public function destroy($id){
        $paletSalida=PaletSalida::where('id',$id)->first();
        $jabas=JabaSalida::where('palet_salida_id',$paletSalida->id)->get();
        foreach ($jabas as $key => $jaba) {
            $jaba->delete();
        }
        $paletSalida->delete();
        return response()->json([
            "status" => "OK"
        ]);
    }

}

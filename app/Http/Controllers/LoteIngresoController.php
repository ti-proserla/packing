<?php

namespace App\Http\Controllers;

use App\Model\LoteIngreso;
use App\Model\PaletSalida;
use App\Http\Requests\LoteIngresoValidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoteIngresoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        if ($request->has('estado')) {
            $lotesIngreso=LoteIngreso::join('cliente','cliente.id','=','lote_ingreso.cliente_id')
                            ->join('materia','materia.id','=','lote_ingreso.materia_id')
                            ->join('variedad','variedad.id','=','lote_ingreso.variedad_id')
                            ->join('tipo','tipo.id','=','lote_ingreso.tipo_id')
                            ->select('lote_ingreso.*','cliente.descripcion','materia.nombre_materia','variedad.nombre_variedad','tipo.nombre_tipo')
                            ->where('estado',$request->estado)
                            ->get();
            }else{
                $lotesIngreso=LoteIngreso::join('cliente','cliente.id','=','lote_ingreso.cliente_id')
                            ->join('materia','materia.id','=','lote_ingreso.materia_id')
                            ->join('variedad','variedad.id','=','lote_ingreso.variedad_id')
                            ->join('tipo','tipo.id','=','lote_ingreso.tipo_id')
                            ->select('lote_ingreso.*','cliente.descripcion','materia.nombre_materia','variedad.nombre_variedad','tipo.nombre_tipo')
                            ->get();
        }
        return response()->json($lotesIngreso); 
    }

    public function generar_codigo(Request $request){
        $materia_id=$request->materia_id;
        $variedad_id=$request->variedad_id;
        $cliente_id=$request->cliente_id;
        $fundo_id=$request->fundo_id;
        $fecha_cosecha=$request->fecha_cosecha;
        $query="SELECT 
                CONCAT(
                    'J',
                    (SELECT cod_cartilla FROM cliente where id=$cliente_id),
                    (SELECT cod_cartilla FROM fundo where id=$fundo_id),
                    (SELECT cod_cartilla FROM materia where id=$materia_id),
                    (SELECT cod_cartilla FROM variedad where id=$variedad_id),
                    SUBSTRING(YEAR('$fecha_cosecha'),-1,1),
                    DAYOFYEAR(DATE_FORMAT('$fecha_cosecha', '2016-%m-%d'))
                ) codigo";
        $data=DB::select(DB::raw("$query"),[])[0];      
        return response()->json($data);  
    }

    public function palets_salida(Request $request){
        $loteIngreso=LoteIngreso::with('palets_salida')
                            ->join('cliente','cliente.id','=','lote_ingreso.cliente_id')
                            ->join('materia','materia.id','=','lote_ingreso.materia_id')
                            ->join('variedad','variedad.id','=','lote_ingreso.variedad_id')
                            ->join('tipo','tipo.id','=','lote_ingreso.tipo_id')
                            ->select('lote_ingreso.*',
                                    'cliente.descripcion as cliente',
                                    'materia.nombre_materia as materia',
                                    'variedad.nombre_variedad as variedad',
                                    'tipo.nombre_tipo as tipo'
                            )
                            ->where('estado',$request->estado)
                            ->get();
        return response()->json($loteIngreso);
    }

    public function store(LoteIngresoValidate $request)
    {
        $loteIngreso=new LoteIngreso();
        $loteIngreso->codigo=$request->codigo;
        $loteIngreso->cliente_id=$request->cliente_id;
        $loteIngreso->materia_id=$request->materia_id;
        $loteIngreso->variedad_id=$request->variedad_id;
        $loteIngreso->tipo_id=$request->tipo_id;
        $loteIngreso->fundo_id=$request->fundo_id;
        $loteIngreso->parcela_id=$request->parcela_id;
        $loteIngreso->fecha_cosecha=$request->fecha_cosecha;
        $loteIngreso->estado="Pendiente";
        $loteIngreso->save();
        return response()->json([
            "status" => "OK",
            "data"   => $loteIngreso,
        ]);
    }

    public function show($id)
    {
        $loteIngreso=LoteIngreso::where('lote_ingreso.id',$id)
                    ->join('cliente','cliente.id','=','lote_ingreso.cliente_id')
                    ->select('lote_ingreso.*','cliente.ruc','cliente.descripcion')
                    ->first();
        return response()->json($loteIngreso);
    }

    public function update(Request $request, $id)
    {
        $loteIngreso=LoteIngreso::where('id',$id)->first();
        $loteIngreso->estado=$request->estado;
        $loteIngreso->save();
        return response()->json([
            "status" => "OK",
            "data"   => "Lote Ingreso Registrado."
        ]);
    }

   
    public function destroy($id)
    {
        $loteIngreso=LoteIngreso::where('id',$id)->first();
        $loteIngreso->delete();
        return response()->json([
            "status" => "OK"
        ]);
    }
    
}


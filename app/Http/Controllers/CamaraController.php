<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Camara;
use App\Model\Posicion;
use App\Model\operacion;
use Illuminate\Support\Facades\DB;

class CamaraController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(Camara::all());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $camara=Camara::where('codigo',$id)->first();
        $pisos=array();
        for ($k=0; $k < $camara->pisos; $k++) { 
            $datos=array();
            for ($i=0; $i < $camara->matriz_y; $i++) { 
                for ($j=0; $j < $camara->matriz_x; $j++) { 
                    $datos[$i][$j]=Posicion::where('codigo_camara',$id)
                                    ->where('x',$i)
                                    ->where('y',$j)
                                    ->where('piso',($k+1))
                                    ->leftJoin('sku','sku.posicion_id','=','posicion.id')
                                    ->leftJoin('palet_salida as ps','ps.id','=','sku.palet_id')
                                    ->leftJoin('cliente as cl','cl.id','=','ps.cliente_id')
                                    ->select('posicion.id','posicion.codigo',DB::raw('DATE(sku.ingreso) ingreso'),DB::raw('CONCAT(ps.campania_id," ",ps.tipo_palet_id," ",cl.cod_cartilla,"-",ps.numero) palet'))
                                    ->first();
                }
            }
            $pisos[]=$datos;
        }
        return response()->json($pisos);
    }
    
    public function operacion($codigo_operacion,Request $request)
    {
        $operacion=Operacion::where('codigo_operacion',$codigo_operacion)->first();
        // dd($operacion);
        $operacion_id=$operacion->id;
        $queryLista="SELECT posicion.codigo_camara, posicion.piso, GROUP_CONCAT('',posicion.codigo) posicion
                    FROM posicion 
                    LEFT JOIN sku ON sku.posicion_id=posicion.id
                    LEFT JOIN palet_salida as ps ON ps.id=sku.palet_id
                    WHERE ps.operacion_id=$operacion_id
                    GROUP BY posicion.codigo_camara, posicion.piso
                    ";
        $listaDeCamaras=DB::select(DB::raw($queryLista), []);
        if ($request->has('min_data')) {
            return response()->json($listaDeCamaras);
        }
        $pisos=array();
        foreach ($listaDeCamaras as $key => $preCamara) {
            $camara=Camara::where('codigo',$preCamara->codigo_camara)->first();
            $piso=[
                "titulo"    => "$preCamara->codigo_camara - PISO $preCamara->piso",
                "datos"     =>[]
            ];
            $datos=array();
            for ($i=0; $i < $camara->matriz_y; $i++) { 
                for ($j=0; $j < $camara->matriz_x; $j++) { 
                    $datos[$i][$j]=Posicion::where('codigo_camara',$preCamara->codigo_camara)
                                    ->where('x',$i)
                                    ->where('y',$j)
                                    ->where('piso',$preCamara->piso)
                                    ->leftJoin('sku','sku.posicion_id','=','posicion.id')
                                    ->leftJoin('palet_salida as ps', function ($join)  use ($operacion_id) {
                                        $join->on('ps.id','=','sku.palet_id')
                                                ->where('ps.operacion_id',$operacion_id);
                                    })
                                    ->leftJoin('cliente as cl','cl.id','=','ps.cliente_id')
                                    ->select(
                                        'posicion.id',
                                        'posicion.codigo',
                                        DB::raw('DATE(sku.ingreso) ingreso'),
                                        DB::raw('CONCAT(ps.campania_id," ",ps.tipo_palet_id,"-",ps.numero) palet'))
                                    ->first();
                                    // dd($datos[$i][$j]);
                }
            }
            $piso["datos"]=$datos;
            $pisos[]=$piso;
        }
        return response()->json($pisos);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}

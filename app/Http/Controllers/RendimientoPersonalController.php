<?php

namespace App\Http\Controllers;
use App\Model\RendimientoPersonal;
use App\Http\Requests\RendimientoPersonalValidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RendimientoPersonalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->has('all')) {
            $rendimiento=RendimientoPersonal::where('fecha_empaque',$request->fecha_empaque)
                        ->all();
        }else{
            $rendimiento=RendimientoPersonal::where('fecha_empaque',$request->fecha_empaque)
                        ->paginate(10);
        }
        return response()->json($rendimiento);
    }

    public function store(Request $request)
    {

        $codigo=$request->codigo;
        $presentacion_id=$request->presentacion_id;
        $fecha_empaque=$request->fecha_empaque;

        $query="SELECT codigo_barras 
                FROM (
                    SELECT * FROM rendimiento_personal 
                    where caja_id is NULL 
                    order by id DESC limit 100
                ) RP 
                WHERE RP.codigo_barras=?";
        $data=DB::select(DB::raw($query),[$codigo]);
        if (count($data)==0) {
            $rendimientoPersonal=new RendimientoPersonal();
            $rendimientoPersonal->codigo_barras=$codigo;
            $rendimientoPersonal->codigo_operador=substr($codigo,4,8);
            $rendimientoPersonal->linea=substr($codigo,0,2);
            $rendimientoPersonal->codigo_labor=substr($codigo,2,2);
            $rendimientoPersonal->presentacion_id=$presentacion_id;
            $rendimientoPersonal->fecha_empaque=$fecha_empaque;
            $rendimientoPersonal->save();
            return response()->json([
                "status" => "OK"
            ]);   
        }else{
            return response()->json([
                "status" => "ERROR",
                "message"=> "Código ya registrado"
            ]);  
        }
    }
    
    
    
    public function show($id)
    {
        $materias=RendimientoPersonal::where('id',$id)->first();
        return response()->json($materias);
    }
    
    
    
    public function update(RendimientoPersonalValidate $request, $id)
    {
        $materias=RendimientoPersonal::where('id',$id)->first();
        $materias->cod_cartilla=$request->cod_cartilla;
        $materias->nombre_materia=$request->nombre_materia;
        $materias->save();

        return response()->json([
            "status" => "OK"
        ]);
    }


    public function destroy($id)
    {
        $materias=RendimientoPersonal::where('id',$id)->first();
        $materias->delete();
        return response()->json([
            "status" => "OK"
        ]);
    }
}

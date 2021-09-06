<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\PaletEntrada;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LanzadoController extends Controller
{
    /**
     * 
     */
    public function index(){
        $query="SELECT 	LI.codigo,
                        LI.fecha_proceso,
                        CL.descripcion productor,
                        MA.nombre_materia,
                        VA.nombre_variedad,
                        PE.linea_lanzado,
                        PE.num_palet,
                        PE.fecha_lanzado,
                        PE.fecha_fin_lanzado
                FROM palet_entrada PE 
                INNER JOIN sub_lote SL ON SL.id=PE.sub_lote_id
                INNER JOIN lote_ingreso LI ON LI.id=SL.lote_id
                INNER JOIN cliente CL ON CL.id=LI.cliente_id
                INNER JOIN materia MA ON MA.id=LI.materia_id
                INNER JOIN variedad VA ON VA.id=LI.variedad_id
                WHERE PE.estado='Lanzado'
                -- AND LI.fecha_proceso=''
                ORDER BY PE.fecha_lanzado DESC";
        $data=DB::select(DB::raw("$query"),[]);      
        return response()->json($data); 
    }

    public function palet_entrada(Request $request){
        $palet_id=explode("-",$request->codigo)[1];
        $palet_entrada=PaletEntrada::where('id',$palet_id)
                        // ->where('estado','Pendiente')
                        ->first(); 
        switch ($palet_entrada->estado) {
            case 'Pendiente':
                $palet_anterior=PaletEntrada::where('linea_lanzado',$request->linea)
                                        ->orderBy('fecha_lanzado','DESC')
                                        ->first();
                $palet_anterior->fecha_fin_lanzado=Carbon::now();
                $palet_anterior->save();
                $palet_entrada->estado='Lanzado';
                $palet_entrada->linea_lanzado=$request->linea;
                $palet_entrada->fecha_lanzado=Carbon::now();
                $palet_entrada->save();                
                return response()->json([
                    "status" => "OK",
                    "data"   => "Palet lanzado por Linea ". $request->linea
                ]);
                break;
                
            case 'Lanzado':
                return response()->json([
                    "status" => "ERROR",
                    "data"   => "Palet ya fue Lanzado"
                ]);
                break;
        }
    }
}

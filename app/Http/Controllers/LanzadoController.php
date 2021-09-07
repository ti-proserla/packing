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
    public function index(Request $request){
        $fecha_proceso=$request->fecha_proceso;
        $query="SELECT 	PE.id,
                        LI.codigo,
                        LI.fecha_proceso,
                        LI.fecha_cosecha,
                        CL.descripcion productor,
                        MA.nombre_materia,
                        VA.nombre_variedad,
                        PE.linea_lanzado,
                        PE.num_palet,
                        DATE_FORMAT(PE.fecha_lanzado,'%H:%i') inicio,
                        DATE_FORMAT(PE.fecha_fin_lanzado,'%H:%i') fin,
                        TIMESTAMPDIFF(MINUTE, PE.fecha_lanzado, PE.fecha_fin_lanzado ) diferencia
                FROM palet_entrada PE 
                INNER JOIN sub_lote SL ON SL.id=PE.sub_lote_id
                INNER JOIN lote_ingreso LI ON LI.id=SL.lote_id
                INNER JOIN cliente CL ON CL.id=LI.cliente_id
                INNER JOIN materia MA ON MA.id=LI.materia_id
                INNER JOIN variedad VA ON VA.id=LI.variedad_id
                WHERE PE.estado='Lanzado'
                AND LI.fecha_proceso=?
                ORDER BY PE.fecha_lanzado DESC";
        $data=DB::select(DB::raw("$query"),[$fecha_proceso]);      
        return response()->json($data); 
    }

    public function palet_entrada(Request $request){
        $palet_id=explode("-",$request->codigo)[1];
        $palet_entrada=PaletEntrada::find($palet_id);

        switch ($palet_entrada->estado) {
            case 'Pendiente':

                $palet_anterior=PaletEntrada::where('linea_lanzado',$request->linea)
                                        ->orderBy('fecha_lanzado','DESC')
                                        ->first();
                if ($palet_anterior!=null) {
                    if ($palet_anterior->fecha_fin_lanzado==null) {
                        $palet_anterior->fecha_fin_lanzado=Carbon::now();
                        $palet_anterior->save();
                    }
                }
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
    public function cerrar(Request $request,$id){
        $palet_entrada=PaletEntrada::find($id);
        $palet_entrada->fecha_fin_lanzado=Carbon::now();
        $palet_entrada->save();
        return response()->json([
            "status" => "OK",
            "message"   => "Palet Cerrado"
        ]);
    }
}

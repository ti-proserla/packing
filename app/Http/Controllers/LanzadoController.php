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
        $query="SELECT LI.codigo,PE.linea_lanzado,COUNT(PE.id) palets
                FROM palet_entrada PE 
                INNER JOIN sub_lote SL ON SL.id=PE.sub_lote_id
                INNER JOIN lote_ingreso LI ON LI.id=SL.lote_id
                WHERE PE.estado='Lanzado'
                GROUP BY LI.codigo,PE.linea_lanzado";
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

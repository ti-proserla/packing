<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Descarte;
use App\Model\SubLote;
use Illuminate\Support\Facades\DB;

class DescarteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->has('all')) {
            $descarte=Descarte::all();
        }else{
            $descarte=Descarte::join('lote_ingreso as LI','LI.id','=','descarte.lote_id')->paginate(10);
        }
        return response()->json($descarte);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            
            $descarte=new Descarte();
            $descarte->descarte_racimos=$request->descarte_racimos;
            $descarte->descarte_granos=$request->descarte_granos;
            $descarte->cantidad_jabas_descarte=$request->cantidad_jabas_descarte;
            $descarte->lote_id=$request->lote_id;
            $descarte->save();
    
            $query="SELECT 	SL.id,SL.lote_id, 
                                    ROUND(
                                        SUM(PE.peso-PE.peso_palet-PE.num_jabas*PE.peso_jaba)*DES.descarte_racimos
                                        /DES.peso_total_lote
                                        ,2) descarte_racimos, 
                                    ROUND(
                                        SUM(PE.peso-PE.peso_palet-PE.num_jabas*PE.peso_jaba)*DES.descarte_granos
                                        /DES.peso_total_lote
                                        ,2) descarte_granos
                            FROM sub_lote SL
                            LEFT JOIN palet_entrada PE ON SL.id=PE.sub_lote_id
                            LEFT JOIN (
                            SELECT 	DE.*,
                                            SUM(PE.peso-PE.peso_palet-PE.num_jabas*PE.peso_jaba) peso_total_lote 
                            FROM descarte DE
                            LEFT JOIN sub_lote SL ON DE.lote_id = SL.lote_id
                            LEFT JOIN palet_entrada PE ON SL.id=PE.sub_lote_id
                            GROUP BY DE.lote_id
                            ) DES ON DES.lote_id=SL.lote_id
                            WHERE SL.lote_id=?
                            GROUP BY SL.id";
            $data=DB::select(DB::raw("$query"),[$request->lote_id]);
            foreach ($data as $key => $row) {
                $subLote=SubLote::where('id',$row->id)->first();
                $subLote->descarte_racimos=$row->descarte_racimos;
                $subLote->descarte_granos=$row->descarte_granos;
                $subLote->save();
            }
            DB::commit();
            return response()->json([
                "status" => "OK",
                "data"  => $descarte,
                "message"=> "Descarte Registrado"
            ]);
        } catch (\Exception $ex) {
            DB::rollback();
            
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $descarte=Descarte::where('id',$id)->first();
        return response()->json($descarte);
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
        DB::beginTransaction();
        try {
            $descarte=Descarte::where('id',$id)->first();
            $descarte->descarte_racimos=$request->descarte_racimos;
            $descarte->descarte_granos=$request->descarte_granos;
            $descarte->cantidad_jabas_descarte=$request->cantidad_jabas_descarte;
            $descarte->lote_id=$request->lote_id;
            $descarte->save();

            $query="SELECT 	SL.id,SL.lote_id, 
                                    ROUND(
                                        SUM(PE.peso-PE.peso_palet-PE.num_jabas*PE.peso_jaba)*DES.descarte_racimos
                                        /DES.peso_total_lote
                                        ,2) descarte_racimos, 
                                    ROUND(
                                        SUM(PE.peso-PE.peso_palet-PE.num_jabas*PE.peso_jaba)*DES.descarte_granos
                                        /DES.peso_total_lote
                                        ,2) descarte_granos
                            FROM sub_lote SL
                            LEFT JOIN palet_entrada PE ON SL.id=PE.sub_lote_id
                            LEFT JOIN (
                            SELECT 	DE.*,
                                            SUM(PE.peso-PE.peso_palet-PE.num_jabas*PE.peso_jaba) peso_total_lote 
                            FROM descarte DE
                            LEFT JOIN sub_lote SL ON DE.lote_id = SL.lote_id
                            LEFT JOIN palet_entrada PE ON SL.id=PE.sub_lote_id
                            GROUP BY DE.lote_id
                            ) DES ON DES.lote_id=SL.lote_id
                            WHERE SL.lote_id=?
                            GROUP BY SL.id";
            $data=DB::select(DB::raw("$query"),[$request->lote_id]);
            foreach ($data as $key => $row) {
                $subLote=SubLote::where('id',$row->id)->first();
                $subLote->descarte_racimos=$row->descarte_racimos;
                $subLote->descarte_granos=$row->descarte_granos;
                $subLote->save();
            }
            DB::commit();
            return response()->json([
                "status" => "OK",
                "data"  => $descarte,
                "message"=> "Descarte Actualizado"
            ]);
        } catch (\Exception $ex) {
            DB::rollback();
            
        }
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

<?php

namespace App\Http\Controllers;
use App\Model\Fundo;
use App\Http\Requests\FundoValidate;
use Illuminate\Http\Request;

class FundoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->has('all')) {
            if ($request->has('productor_id')) {
                $fundos=Fundo::where('productor_id',$request->productor_id)
                    ->get();
            }else{
                $fundos=Fundo::all();
            }

        }else{
            $fundos=Fundo::paginate(10);
        }
        return response()->json($fundos);
    }

    public function detallado(){
        $fundos=Fundo::with('parcelas')->get();
        return response()->json($fundos);
    }

   
    public function store(FundoValidate $request)
    {
        $fundos=new Fundo();
        $fundos->cod_cartilla=$request->cod_cartilla;
        $fundos->nombre_fundo=$request->nombre_fundo;
        $fundos->lugar_produccion=$request->lugar_produccion;
        $fundos->cod_lugar_produccion=$request->cod_lugar_produccion;
        $fundos->productor_id=$request->productor_id;
        $fundos->save();
        return response()->json([
            "status" => "OK"
        ]);

    }



    public function show($id)
    {
        $fundos=Fundo::where('id',$id)->first();
        return response()->json($fundos);
    }


  
    public function update(FundoValidate $request, $id)
    {
        $fundos=Fundo::where('id',$id)->first();
        $fundos->cod_cartilla=$request->cod_cartilla;
        $fundos->nombre_fundo=$request->nombre_fundo;
        $fundos->lugar_produccion=$request->lugar_produccion;
        $fundos->cod_lugar_produccion=$request->cod_lugar_produccion;
        $fundos->productor_id=$request->productor_id;
        $fundos->save();

        return response()->json([
            "status" => "OK"
        ]);
    }


    public function destroy($id)
    {
        $fundos=Fundo::where('id',$id)->first();
        $fundos->delete();
        return response()->json([
            "status" => "OK"
        ]);
    }
}

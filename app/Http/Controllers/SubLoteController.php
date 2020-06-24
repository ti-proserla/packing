<?php

namespace App\Http\Controllers;
use App\Http\Requests\SubLoteValidate;
use Illuminate\Http\Request;
use App\Model\SubLote;

class SubLoteController extends Controller
{
   
    public function index($id)
    {
        $subLotes=SubLote::with('transportista')->where('lote_id',$id)->get();
        return response()->json($subLotes);
    }

    
   
    public function store(SubLoteValidate $request)
    {
        $subLotes=new SubLote();
        $subLotes->lote_id=$request->lote_id;
        $subLotes->guia=$request->guia;
        $subLotes->transportista_id=$request->transportista_id;
        $subLotes->save();

        return response()->json([
            "status" => "OK"
        ]);
    }

    public function show($id)
    {
        //
    }

   
   
    public function update(SubLoteValidate $request, $id)
    {
        //
    }

    
    public function destroy($id)
    {
        //
    }
}

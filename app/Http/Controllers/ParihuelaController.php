<?php

namespace App\Http\Controllers;
use App\Model\Parihuela;
use App\Http\Requests\ParihuelaValidate;
use Illuminate\Http\Request;

class ParihuelaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->has('all')) {
            $parihuela=Parihuela::all();
        }else{
            $parihuela=Parihuela::paginate(10);
        }
        return response()->json($parihuela);
    }
   
    public function store(ParihuelaValidate $request)
    {
        $parihuela=new Parihuela();
        $parihuela->nombre_parihuela=$request->nombre_parihuela;
        $parihuela->save();
        return response()->json([
            "status" => "OK"
        ]);

    }



    public function show($id)
    {
        $parihuela=Parihuela::where('id',$id)->first();
        return response()->json($parihuela);
    }


  
    public function update(ParihuelaValidate $request, $id)
    {
        $parihuela=Parihuela::where('id',$id)->first();
        $parihuela->nombre_parihuela=$request->nombre_parihuela;
        $parihuela->save();

        return response()->json([
            "status" => "OK"
        ]);
    }


    public function destroy($id)
    {
        $parihuelas=Parihuela::where('id',$id)->first();
        $parihuelas->delete();
        return response()->json([
            "status" => "OK"
        ]);
    }
}

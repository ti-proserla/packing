<?php

namespace App\Http\Controllers;
use App\Http\Requests\PLUValidate;
use App\Model\PLU;
use Illuminate\Http\Request;

class PLUController extends Controller
{
   
    public function index(Request $request)
    {
        if ($request->has('all')) {
            $plus=PLU::all();
        }else {
            $plus=PLU::paginate(10);
        }
        return response()->json($plus);
    }

    public function store(PLUValidate $request)
    {
        $plus=new PLU();
        $plus->nombre_plu=$request->nombre_plu;
        $plus->save();

        return response()->json([
            "status" => "OK"
        ]);

    }

    public function show($id)
    {
        $plus=PLU::where('id',$id)->first();
        return response()->json($plus);
    }


    
    public function update(PLUValidate $request, $id)
    {
        $plus=PLU::where('id',$id)->first();
        $plus->nombre_plu=$request->nombre_plu;
        $plus->save();

        return response()->json([
            "status" => "OK"
        ]);
    }

    
    public function destroy($id)
    {
        $plus=PLU::where('id',$id)->first();
        $plus->delete();
        return response()->json([
            "status" => "OK"
        ]);
    }
}

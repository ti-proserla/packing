<?php

namespace App\Http\Controllers;
use App\Http\Requests\CalibreValidate;
use App\Model\Calibre;
use Illuminate\Http\Request;

class CalibreController extends Controller
{
   
    public function index(Request $request)
    {
        if ($request->has('all')) {
            $Calibrees=Calibre::all();
        }else {
            $Calibrees=Calibre::select('calibre.*','nombre_materia')
                                    ->join('materia','materia.id','=','calibre.materia_id')
                                    ->paginate(10);
        }
        return response()->json($Calibrees);
    }

    public function store(CalibreValidate $request)
    {
        $Calibrees=new Calibre();
        $Calibrees->nombre_calibre=$request->nombre_calibre;
        $Calibrees->materia_id=$request->materia_id;
        $Calibrees->save();

        return response()->json([
            "status" => "OK"
        ]);

    }

    public function show($id)
    {
        $Calibrees=Calibre::where('id',$id)->first();
        return response()->json($Calibrees);
    }


    
    public function update(CalibreValidate $request, $id)
    {
        $Calibrees=Calibre::where('id',$id)->first();
        $Calibrees->nombre_calibre=$request->nombre_calibre;
        $Calibrees->materia_id=$request->materia_id;
        $Calibrees->save();

        return response()->json([
            "status" => "OK"
        ]);
    }

    
    public function destroy($id)
    {
        $Calibrees=Calibre::where('id',$id)->first();
        $Calibrees->delete();
        return response()->json([
            "status" => "OK"
        ]);
    }
}

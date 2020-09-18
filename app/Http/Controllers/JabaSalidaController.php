<?php

namespace App\Http\Controllers;

use App\Model\JabaSalida;
use Illuminate\Http\Request;

class JabaSalidaController extends Controller
{
    public function store(Request $request)
    {
        $loteIngreso=new LoteIngreso();
        $loteIngreso->codigo=$request->codigo;
        $loteIngreso->cliente_id=$request->cliente_id;
        $loteIngreso->materia_id=$request->materia_id;
        $loteIngreso->variedad_id=$request->variedad_id;
        $loteIngreso->fecha_cosecha=$request->fecha_cosecha;
        $loteIngreso->estado="Registrado";
        $loteIngreso->save();
        return response()->json([
            "status" => "OK",
            "data"   => $loteIngreso,
        ]);
    }
}

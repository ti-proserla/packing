<?php

namespace App\Http\Controllers;

use App\Model\Tareo;
use App\Model\Parametro;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Model\Labor;
use App\Model\EtiquetaCaja;


class PrintZPLController extends Controller
{
    /**
     * ip_print: x.x.x.x    => IP de Impresora Zebra.
     * codigo_operador
     */
    public function cajas(Request $request){
        $ip_print = $request->ip_print;
        if (!$request->has('return')) {
            if (!$this->ping($ip_print)){
                return response()->json([
                    "status"    => "ERROR",
                    "data"      => "Impresora Desconectada."
                ]);
            }
        }
        $codigo_operador = $request->codigo_operador;

        $tareo=Tareo::where('codigo_operador',$codigo_operador)
                    ->select('tareo.*','operador.ape_operador')
                    ->join('operador','tareo.codigo_operador','=','operador.dni')
                    ->orderBy('id','DESC')
                    ->first();
        $nombre_operador=$tareo->ape_operador;

        if ($tareo==null) {
           return response()->json([
               "status"    => "ERROR",
               "data"      => "Tareo no existe."
           ]);
        }
        // dd($tareo);
        $labor=Labor::where('codigo_auxiliar','like','%'.$tareo->labor_id.'%')
            ->first();
        // dd($labor);
        
        if ($labor==null) {
            return response()->json([
                "status"    => "ERROR",
                "data"      => "Labor no permitida."
            ]);
        }
        $labor_letra=($labor->descripcion)[0];
        $labor_id=$labor->codigo_labor;
        // $linea_id=str_pad($tareo->linea_id, 2, "0", STR_PAD_LEFT);
        $linea_id=($tareo->linea_id==1) ? '00': str_pad($tareo->linea_id - 1, 2, "0", STR_PAD_LEFT);
            $string_zpl="^XA
                ^FT20,30
                ^AAN,21,10
                ^FB240,1,0,C
                ^FD{nombre_operador}^FS
                ^FT140,100
                ^AAN,40,15
                ^FD{labor_letra}^FS
                ^FT20,170
                ^BQN,2,5
                ^FDMA,{linea}{labor}{operador}{autonumerico}^FS
                ^XZ";
            $parametros=array(
                'linea'         =>  $linea_id,
                'operador'      =>  $codigo_operador,
                'labor'         =>  $labor_id,
                'labor_letra'   =>  $labor_letra,
                'nombre_operador'=> $nombre_operador
            );
        
            // foreach($data[$i*$columna+$j] as $key=>$value){
            //     $string_zpl_bk=str_replace('['.$key.']',$value,$string_zpl_bk);
            // }
            $columna=2;
            $string_zpl=str_replace('^XA','',$string_zpl);
            $string_zpl=str_replace('^XZ','',$string_zpl);
            
            $string_zpl_new="";
            $string_zpl_new.="^XA";
            for ($j=0; $j < $columna; $j++) {
                $string_zpl_new.=$this->columnaEtiqueta($string_zpl,$j,420);
            }
            $string_zpl_new.="^XZ";
        if ($request->has('return')) {
            return response()->json([
                "status"    =>  "OK",
                "data"      =>  $this->cast_zpl($string_zpl_new,$parametros)
            ]);
        }else{
            $this->print_red($ip_print,9100,$this->cast_zpl($string_zpl_new,$parametros));
            return response()->json([
                "status" => "OK",
                "data"   => "Imprimiendo."

            ]);
        }
    }
    public function caja_palet(Request $request){
        $ip_print=$request->ip_print;
        $codigo="P-".$request->codigo;
        if ($this->ping($ip_print)){            
            $string_zpl="^XA
                        ^FO15,15
                        ^BQN,2,4
                        ^FDQA,$codigo^FS
                        ^XZ";
            $this->print_red($ip_print,9100,$string_zpl);
            return response()->json([
                "status" => "OK",
                "data"   => "Imprimiendo."
            ]);
        }else{
            return response()->json([
                "status"    => "ERROR",
                "data"      => "Impresora Desconectada."
            ]);
        }
    }
    public function palet_entrada_all(Request $request){
        $ip_print = $request->ip_print;
        $sub_lote_id=$request->sub_lote_id;

        $w_etiqueta=410;
        if ($this->ping($ip_print)){            
            $string_zpl="^XA
                            ^FT25,30
                            ^AAN,21,10
                            ^FB360,1,0,C
                            ^FD[empresa]^FS
                            
                            ^FT140,70
                            ^AAN,21,10
                            ^FD[variedad]^FS
                            
                            ^FT140,100
                            ^AAN,21,10
                            ^FDN. Jabas: [num_jabas]^FS

                            ^FT140,130
                            ^AAN,21,10
                            ^FDN. Viaje: [viaje]^FS
                            
                            ^FT25,160
                            ^AAN,30,15
                            ^FB360,1,0,R
                            ^FD[num_palet]^FS
                        
                            ^FT20,170
                            ^BQN,2,5
                            ^FDMA,P-[palet_id]^FS
                        ^XZ";
            dd(preg_replace("[\n|\r|\n\r|/\s+/g]", "",$string_zpl));      
            $string_zpl=
            $string_zpl=str_replace('^XA','',$string_zpl);
            $string_zpl=str_replace('^XZ','',$string_zpl);
            $query="SELECT 	PE.num_palet,
                            codigo, 
                            CL.descripcion empresa,
                            PE.peso peso, 
                            VA.nombre_variedad variedad,
                            PE.num_jabas,
                            SL.viaje,
                            PE.id palet_id
                    FROM lote_ingreso LI 
                    INNER JOIN cliente CL ON CL.id=LI.cliente_id
                    INNER JOIN sub_lote SL ON SL.lote_id=LI.id
                    INNER JOIN palet_entrada PE ON PE.sub_lote_id=SL.id
                    INNER JOIN variedad VA on LI.variedad_id=VA.id
                    WHERE sub_lote_id=?
                    ORDER BY palet_id DESC";
            $data=DB::select(DB::raw("$query"),[$sub_lote_id]);
            
            $columna=2;
            $string_zpl_new="";
            $data_part=array_chunk($data,$columna);
            for ($i=0; $i < count($data_part); $i++) {
                $data_row=$data_part[$i];
                for ($j=0; $j < count($data_row); $j++) { 
                    
                    $string_zpl_bk=$this->columnaEtiqueta($string_zpl,$j);
                    foreach($data_row[$j] as $key=>$value){
                        $string_zpl_bk=str_replace('['.$key.']',$value,$string_zpl_bk);
                    }
                    $string_zpl_new.=$string_zpl_bk;
                }
                $string_zpl_new.="^XA$string_zpl_bk^XZ";
            }
            for ($i=0; $i < count($data)/$columna; $i++) {
                $recorrido=($i+1)*$columna<count($data) ? $columna : (count($data)-($i)*$columna);
                $string_zpl_new.="^XA";
                for ($j=0; $j < $recorrido; $j++) {
                    
                    $string_zpl_bk=$this->columnaEtiqueta($string_zpl,$j);
                    foreach($data[$i*$columna+$j] as $key=>$value){
                        $string_zpl_bk=str_replace('['.$key.']',$value,$string_zpl_bk);
                    }
                    $string_zpl_new.=$string_zpl_bk;
                }
                $string_zpl_new.="^XZ";
                $this->print_red($ip_print,9100,$string_zpl_new);
                $string_zpl_new="";
            }            
            
            dd(preg_replace("[\n|\r|\n\r]", "",$string_zpl_new));
            return response()->json([
                "status" => "OK",
                "data"   => "Imprimiendo."
            ]);
        }
        else {
            return response()->json([
                "status"    => "ERROR",
                "data"      => "Impresora Desconectada."
            ]);
        }
    }

    public function palet_entrada(Request $request){
        $ip_print = $request->ip_print;
        $palet_entrada_id=$request->palet_entrada_id;

        $w_etiqueta=410;
            $string_zpl="^XA
                            ^FT25,30
                            ^AAN,21,10
                            ^FB360,1,0,C
                            ^FD[empresa]^FS
                            
                            ^FT140,70
                            ^AAN,21,10
                            ^FD[variedad]^FS
                            
                            ^FT140,100
                            ^AAN,21,10
                            ^FDN. Jabas: [num_jabas]^FS

                            ^FT140,130
                            ^AAN,21,10
                            ^FDN. Viaje: [viaje]^FS
                            
                            ^FT25,160
                            ^AAN,30,15
                            ^FB360,1,0,R
                            ^FD[num_palet]^FS
                        
                            ^FT20,170
                            ^BQN,2,5
                            ^FDMA,P-[palet_id]^FS
                        ^XZ";
            $string_zpl=str_replace('^XA','',$string_zpl);
            $string_zpl=str_replace('^XZ','',$string_zpl);

            $query="SELECT 	PE.num_palet,
                            codigo, 
                            CL.descripcion empresa,
                            PE.peso peso, 
                            VA.nombre_variedad variedad,
                            PE.num_jabas,
                            SL.viaje,
                            PE.id palet_id
                    FROM lote_ingreso LI 
                    INNER JOIN cliente CL ON CL.id=LI.cliente_id
                    INNER JOIN sub_lote SL ON SL.lote_id=LI.id
                    INNER JOIN palet_entrada PE ON PE.sub_lote_id=SL.id
                    INNER JOIN variedad VA on LI.variedad_id=VA.id
                    WHERE PE.id=?
                    ORDER BY palet_id DESC";
            $data=DB::select(DB::raw("$query"),[$palet_entrada_id]);
            $string_zpl_new="";

            $columna=1;
            
            for ($i=0; $i < count($data)/$columna; $i++) {
                $recorrido=($i+1)*$columna<count($data) ? $columna : (count($data)-($i)*$columna);
                $string_zpl_new.="^XA";
                for ($j=0; $j < $recorrido; $j++) {
                    
                    $string_zpl_bk=$this->columnaEtiqueta($string_zpl,$j);
                    foreach($data[$i*$columna+$j] as $key=>$value){
                        $string_zpl_bk=str_replace('['.$key.']',$value,$string_zpl_bk);
                    }
                    $string_zpl_new.=$string_zpl_bk;
                }
                $string_zpl_new.="^XZ";
            }            

        if ($this->ping($ip_print)){               
            $this->print_red($ip_print,9100,$string_zpl_new);

            return response()->json([
                "status" => "OK",
                "data"   => "Imprimiendo."
            ]);
        }
        else {
            return response()->json([
                "status"    => "ERROR",
                "data"      => "Impresora Desconectada."
            ]);
        }
    }
    public function palet_Salida(Request $request){
        $ip_print = $request->ip_print;
        $palet_id=$request->palet_id;

        $string_zpl="^XA
                    ~TA000
                    ~JSN
                    ^LT0
                    ^MNW
                    ^MTT
                    ^PON
                    ^PMN
                    ^LH0,0
                    ^JMA
                    ^PR8,8
                    ~SD15
                    ^JUS
                    ^LRN
                    ^CI27
                    ^PA0,1,1,0
                    ^XZ
                    ^XA
                    ^MMT
                    ^PW831
                    ^LL1624
                    ^LS0
                    ^FO143,4^GB0,1620,5^FS
                    ^FT49,1604^A0B,23,23^FH\^CI28^FDEXPORTED^FS^CI27
                    ^FT100,1414^A0B,51,51^FH\^CI28^FD[nombre_productor]^FS^CI27
                    ^FT195,1604^A0B,23,23^FH\^CI28^FDPACKED AND PROCESSED^FS^CI27
                    ^FT290,1062^A0B,51,51^FB500,1,13,C^FH\^CI28^FDJAYANCA FRUITS S.A.C^FS^CI27
                    ^FO318,4^GB0,1620,5^FS
                    ^FO516,4^GB0,1620,5^FS
                    ^FT451,1504^A0B,51,51^FB623,1,13,C^FH\^CI28^FDN° DE PALET:  [codigo_palet]^FS^CI27
                    ^FT359,187^BQN,2,6
                    ^FH\^FDLA,[palet_id]^FS
                    ^FO659,4^GB0,1620,5^FS
                    ^FO516,789^GB311,0,5^FS
                    ^FT609,745^A0B,37,38^FH\^CI28^FDN° BOXES^FS^CI27
                    ^FO516,453^GB311,0,5^FS
                    ^FT609,413^A0B,37,38^FH\^CI28^FD[numero_cajas]^FS^CI27
                    ^PQ1,0,1,Y
                    ^XZ";

            $query="SELECT 	CL.descripcion nombre_productor, 
                                CONCAT(LPAD(PS.numero,6,'0'),'-',CL.cod_cartilla) codigo_palet,
                                PS.id palet_id,
                                CAL.nombre_calibre,
                                COUNT(CA.id) numero_cajas
                    FROM palet_salida PS 
                    INNER JOIN cliente CL ON PS.cliente_id=CL.id
                    INNER JOIN caja CA ON CA.palet_salida_id=PS.id
                    INNER JOIN etiqueta_caja EC ON CA.etiqueta_caja_id=EC.id
                    INNER JOIN calibre CAL ON CAL.id = EC.calibre_id
                    WHERE PS.id=?";
        $data=DB::select(DB::raw("$query"),[$palet_id])[0];
        // dd($data);
        $data=json_decode(json_encode($data), true);
        
        foreach($data as $key=>$value){
            $string_zpl=str_replace('['.$key.']',$value,$string_zpl);
        }
        return response()->json($string_zpl);
    }

    public function columnaEtiqueta($zpl,$columna,$width=420)
    {
        preg_match_all("/\^FT(.*?),/",$zpl,$arr);
        $modificado=$zpl;
        for ($i=0; $i < count($arr[0]); $i++) { 
            $x=($arr[1][$i]+$width*$columna);
            $modificado=preg_replace("/\\".$arr[0][$i]."/",'^FT'.$x.',',$modificado,1);
        }
        return $modificado;
    }

    public function print_red($ip,$port,$string_print){
        try
        {    
            $fp=pfsockopen($ip,$port);
            fputs($fp,$string_print);
            fclose($fp);
        }
        catch (Exception $e) 
        {
            dd($e->getMessage());
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
    }

    public function cast_zpl($string_zpl,$parametros){
        foreach($parametros as $key=>$value){
            $string_zpl=str_replace('{'.$key.'}',$value,$string_zpl);
        }

        $print="";
        $number=0;

        $parametro=Parametro::where('descripcion','index_codigo_trabajador')->first();
        $index_db=(int)$parametro->valor;
        $cantidad=50;
        
        if(-1<strpos($string_zpl,'{autonumerico}')){
            $separate_autonumerico=explode('{autonumerico}',$string_zpl);
            $conteo=count($separate_autonumerico);
            // dd($index_db);
            $temp_index_db=$index_db;
            while ($temp_index_db<$index_db+$cantidad ){
                /**
                 * Imprimer entre la separacion
                 */
                for ($i=0; $i < $conteo-1; $i++) { 
                    $value=$separate_autonumerico[$i];
                    $temp_index_db+=1;
                    $index=str_pad($temp_index_db, 4, "0", STR_PAD_LEFT);
                    $print=$print.$value.$index;
                }
                /**
                 * imprime el ultimo retazo
                 */
                $print=$print.$separate_autonumerico[$conteo-1];
            }
        }
        $parametro->valor=$temp_index_db;
        $parametro->save();
        return $print;
    }


    public function myOS(){
        if (strtoupper(substr(PHP_OS, 0, 3)) === (chr(87).chr(73).chr(78)))
            return true;

        return false;
    }

    public function ping($ip_addr){
        if ($this->myOS()){
            if (!exec("ping -n 1 -w 1 ".$ip_addr." 2>NUL > NUL && (echo 0) || (echo 1)"))
                return true;
        } else {
            if (!exec("ping -q -c1 ".$ip_addr." >/dev/null 2>&1 ; echo $?"))
                return true;
        }

        return false;
    }

    public function muestra_etiqueta_caja(Request $request){
        $etiqueta_id=$request->etiqueta_caja_id;
        $etiquetaCaja=EtiquetaCaja::select(
                        DB::raw('CONCAT("C-",etiqueta_caja.id) codigo_caja'),
                        'etiqueta_caja.*',
                        'LI.codigo as codigo_lote',
                        'CLI.descripcion as nombre_productor',
                        'CL.nombre_calibre',
                        'MA.nombre_materia',
                        'VA.nombre_variedad',
                        'CT.nombre_categoria')
                    ->join('calibre as CL','CL.id','=','etiqueta_caja.calibre_id')
                    ->join('categoria as CT','CT.id','=','etiqueta_caja.categoria_id')
                    ->join('lote_ingreso as LI','LI.id','=','etiqueta_caja.lote_ingreso_id')
                    ->join('cliente as CLI','CLI.id','=','LI.cliente_id')
                    ->join('presentacion as PE','PE.id','=','etiqueta_caja.presentacion_id')
                    ->join('materia as MA','MA.id','=','LI.materia_id')
                    ->join('variedad as VA','VA.id','=','LI.variedad_id')
                    ->where('etiqueta_caja.id',$etiqueta_id)
                    ->orderBy('id','DESC')
                    ->first();
        $zpl="^XA
                ~TA000
                ~JSN
                ^LT0
                ^MNW
                ^MTT
                ^PON
                ^PMN
                ^LH0,0
                ^JMA
                ^PR8,8
                ~SD15
                ^JUS
                ^LRN
                ^CI27
                ^PA0,1,1,0
                ^XZ
                ^XA
                ^MMT
                ^PW831
                ^LL609
                ^LS0
                ^FO162,1^GB0,608,3^FS
                ^FPH,1^FT87,430^A0B,14,15^FH\^CI28^FDPACKED BY: JAYANCA FRUITS SAC^FS^CI27
                ^FPH,1^FT149,406^A0B,25,30^FB204,1,6,C^FH\^CI28^FDTABLE GRAPES^FS^CI27
                ^FPH,1^FT191,289^A0B,20,20^FH\^CI28^FDSize: [nombre_calibre]^FS^CI27
                ^FPH,1^FT191,242^A0B,20,20^FB229,1,5,R^FH\^CI28^FDCat: [nombre_categoria]^FS^CI27
                ^FO116,1^GB0,608,3^FS
                ^FO67,1^GB0,608,3^FS
                ^FO165,294^GB57,0,3^FS
                ^FPH,1^FT191,595^A0B,20,20^FH\^CI28^FDVariety: [nombre_variedad]^FS^CI27
                ^FO220,1^GB0,608,3^FS
                ^FT233,131^BQN,2,5
                ^FH\^FDLA,[codigo_caja]^FS
                ^FPH,1^FT31,434^A0B,17,18^FH\^CI28^FDEXPORTED AND PRODUCED BY:^FS^CI27
                ^FPH,1^FT52,436^A0B,17,23^FH\^CI28^FD[nombre_productor]^FS^CI27
                ^FPH,1^FT105,551^A0B,14,15^FH\^CI28^FDCa. ANtigua Panamericana Norte Km. 37 - Jayanca - Lambayeque^FS^CI27
                ^FPH,1^FT215,595^A0B,20,20^FH\^CI28^FDProduct Code: [codigo_lote]^FS^CI27
                ^FO580,1^GB0,608,3^FS
                ^FPH,1^FT505,430^A0B,14,15^FH\^CI28^FDPACKED BY: JAYANCA FRUITS SAC^FS^CI27
                ^FPH,1^FT568,406^A0B,25,30^FB204,1,6,C^FH\^CI28^FDTABLE GRAPES^FS^CI27
                ^FPH,1^FT609,289^A0B,20,20^FH\^CI28^FDSize: [nombre_calibre]^FS^CI27
                ^FPH,1^FT609,242^A0B,20,20^FB229,1,5,R^FH\^CI28^FDCat: [nombre_categoria]^FS^CI27
                ^FO535,1^GB0,608,3^FS
                ^FO485,1^GB0,608,3^FS
                ^FO583,294^GB57,0,3^FS
                ^FPH,1^FT609,595^A0B,20,20^FH\^CI28^FDVariety: [nombre_variedad]^FS^CI27
                ^FO638,1^GB0,608,3^FS
                ^FT651,131^BQN,2,5
                ^FH\^FDLA,[codigo_caja]^FS
                ^FPH,1^FT449,434^A0B,17,18^FH\^CI28^FDEXPORTED AND PRODUCED BY:^FS^CI27
                ^FPH,1^FT470,436^A0B,17,23^FH\^CI28^FD[nombre_productor]^FS^CI27
                ^FPH,1^FT523,551^A0B,14,15^FH\^CI28^FDCa. ANtigua Panamericana Norte Km. 37 - Jayanca - Lambayeque^FS^CI27
                ^FPH,1^FT633,595^A0B,20,20^FH\^CI28^FDProduct Code: [codigo_lote]^FS^CI27
                ^PQ1,0,1,Y
                ^XZ";
        foreach($etiquetaCaja->toArray() as $key=>$value){
            $zpl=str_replace('['.$key.']',$value,$zpl);
        }
        return response()->json($zpl);
    }
}

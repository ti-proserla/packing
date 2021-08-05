<?php

namespace App\Http\Controllers;

use App\Model\Tareo;
use App\Model\Parametro;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Model\Labor;


class PrintZPLController extends Controller
{
    /**
     * ip_print: x.x.x.x    => IP de Impresora Zebra.
     * codigo_operador
     */
    public function cajas(Request $request){
        $ip_print = $request->ip_print;
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
        //dd($tareo->labor_id);
        $labor=Labor::where('codigo_auxiliar','like','%'.$tareo->labor_id.'%')
            ->first();
        $labor_letra=($labor->descripcion)[0];
        
        if ($labor==null) {
            return response()->json([
                "status"    => "ERROR",
                "data"      => "Labor no permitida."
            ]);
        }
        //dd($labor);
        $labor_id=$labor->codigo_labor;
        // $linea_id=str_pad($tareo->linea_id, 2, "0", STR_PAD_LEFT);
        $linea_id=($tareo->linea_id==1) ? '00': str_pad($tareo->linea_id - 1, 2, "0", STR_PAD_LEFT);
        if ($this->ping($ip_print)){            
            $string="^XA
                    ^BY2,1,80
                    ^FO40,35^BCR,,,,,A^FD{linea}{labor}{operador}{autonumerico}^FS
                    ^BY2,1,80
                    ^FO200,35^BCR,,,,,A^FD{linea}{labor}{operador}{autonumerico}^FS
                    ^BY2,1,80
                    ^FO360,35^BCR,,,,,A^FD{linea}{labor}{operador}{autonumerico}^FS
                    ^BY2,1,80
                    ^FO520,35^BCR,,,,,A^FD{linea}{labor}{operador}{autonumerico}^FS
                    ^XZ";
            $string_zpl="^XA
                    ^FT10,50
                    ^AAN,21,10
                    ^FB250,1,0,R
                    ^FD{nombre_operador}^FS

                    ^FT10,50
                    ^AAN,40,15
                    ^FD{labor_letra}^FS

                    ^FT10,130
                    ^BY2,2,60
                    ^BCN,,,,,A
                    ^FD{linea}{labor}{operador}{autonumerico}^FS
                    ^XZ";

            // $string="^XA
            //         ^FO10,10
            //         ^BY2,2,70
            //         ^BCN,,,,,A^FD{linea}{labor}{operador}{autonumerico}^FS
            //         ^FO430,10
            //         ^BY2,2,70
            //         ^BCN,,,,,A^FD{linea}{labor}{operador}{autonumerico}^FS
            //         ^XZ";
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
            $columna=3;
            $string_zpl=str_replace('^XA','',$string_zpl);
            $string_zpl=str_replace('^XZ','',$string_zpl);
            
            $string_zpl_new="";
            $string_zpl_new.="^XA";
            for ($j=0; $j < $columna; $j++) {
                $string_zpl_new.=$this->columnaEtiqueta($string_zpl,$j,280);
            }
            $string_zpl_new.="^XZ";
            
            $this->print_red($ip_print,9100,$this->cast_zpl($string_zpl_new,$parametros));

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
        $sub_lote_id=$request->sub_lote_id;

        $w_etiqueta=410;
        if ($this->ping($ip_print)){            
            $string_zpl="^XA
                    ^FT25,30
                    ^AAN,21,10
                    ^FB360,1,0,C
                    ^FD[empresa]^FS
                    
                    ^FT25,60
                    ^AAN,21,10
                    ^FD[variedad]^FS
                    
                    ^FT25,90
                    ^AAN,21,10
                    ^FDNo Jabas: [num_jabas]^FS
                    
                    ^FT25,70
                    ^AAN,30,10
                    ^FB360,1,0,R
                    ^FD[num_palet]^FS
                    
                    ^BY2,2,30
                    ^FT25,140
                    ^BCN,,Y,N
                    ^FH\
                    ^FDP-[palet_id]^FS
                    ^PQ1,0,1,Y
                    ^XZ";
            $string_zpl=str_replace('^XA','',$string_zpl);
            $string_zpl=str_replace('^XZ','',$string_zpl);

            $query="SELECT 	(@row_number:=@row_number + 1) AS num_palet,
                                codigo, 
                                CL.descripcion empresa,
                                PE.peso peso, 
                                VA.nombre_variedad variedad,
                                PE.num_jabas,
                                PE.id palet_id
                    FROM lote_ingreso LI 
                    INNER JOIN cliente CL ON CL.id=LI.cliente_id
                    INNER JOIN sub_lote SL ON SL.lote_id=LI.id
                    INNER JOIN palet_entrada PE ON PE.sub_lote_id=SL.id
                    INNER JOIN variedad VA on LI.variedad_id=VA.id ,(SELECT @row_number:=0) AS t
                    WHERE sub_lote_id=?
                    ORDER BY palet_id ASC";
            $data=DB::select(DB::raw("$query"),[$sub_lote_id]);
            $string_zpl_new="";

            $columna=2;
            
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
            // dd($string_zpl_new);
            
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
        //dd($index_db);
        $cantidad=4;

        if(-1<strpos($string_zpl,'{autonumerico}')){
            $separate_autonumerico=explode('{autonumerico}',$string_zpl);
            $conteo=count($separate_autonumerico);
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
}

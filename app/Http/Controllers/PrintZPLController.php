<?php

namespace App\Http\Controllers;

use App\Model\Tareo;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
                    ->orderBy('id','DESC')
                    ->first();

        if ($tareo==null) {
            return response()->json([
                "status"    => "ERROR",
                "data"      => "Tareo no existe."
            ]);
        }
        $labor_id=$tareo->labor_id;
        $linea_id=str_pad($tareo->linea_id, 2, "0", STR_PAD_LEFT);
        if ($this->ping($ip_print)){
            try
            {
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
                $parametros=array(
                    'linea'     =>  $linea_id,
                    'operador'  =>  $codigo_operador,
                    'labor'     =>  $labor_id
                );
                foreach($parametros as $key=>$value){
                    $string=str_replace('{'.$key.'}',$value,$string);
                }

                $print="";
                $number=0;

                $index_db=0;
                $cantidad=4;


                if(-1<strpos($string,'{autonumerico}')){
                    $separate_autonumerico=explode('{autonumerico}',$string);
                    $conteo=count($separate_autonumerico);
                    $temp_index_db=$index_db;
                    while ($temp_index_db<$index_db+$cantidad ){
                        /**
                         * Imprimer entre la separacion
                         */
                        for ($i=0; $i < $conteo-1; $i++) { 
                            $value=$separate_autonumerico[$i];
                            $temp_index_db+=1;
                            $index=str_pad($temp_index_db, 8, "0", STR_PAD_LEFT);
                            $print=$print.$value.$index;
                        }
                        /**
                         * imprime el ultimo retazo
                         */
                        $print=$print.$separate_autonumerico[$conteo-1];
                    }

                }else{
                }
                
                $fp=pfsockopen("192.168.1.164",9100);
                fputs($fp,$print);
                fclose($fp);
            }
            catch (Exception $e) 
            {
                echo 'Caught exception: ',  $e->getMessage(), "\n";
            }


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

<?php

use Illuminate\Support\Facades\Route;
use App\Zebra\ZebraGenerate;
Route::get('zebra', function () {
    try
    {

        $string='{linea}{operador}{autonumerico}-{linea}{operador}{autonumerico}';
        $string="^XA
        ^BY1.5,1,80
        ^FO40,10^BCR^FD{linea}{operador}{autonumerico}^FS
        ^BY1.5,1,80
        ^FO200,10^BCR^FD{linea}{operador}{autonumerico}^FS
        ^BY1.5,1,80
        ^FO360,10^BCR^FD{linea}{operador}{autonumerico}^FS
        ^BY1.5,1,80
        ^FO520,10^BCR^FD{linea}{operador}{autonumerico}^FS
        ^XZ";
        $parametros=array(
            'linea'=> '02',
            'operador'=> '773829787'
        );
        foreach($parametros as $key=>$value){
            echo $value;    
            $string=str_replace('{'.$key.'}',$value,$string);
        }

        $print="";
        $number=0;

        $index_db=16;
        $cantidad=16;


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
                    $index=str_pad($temp_index_db, 4, "0", STR_PAD_LEFT);
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
        /*
        $data=ZebraGenerate::text('Hola Mundo',10,20,0); 
        echo $data;
        */
        // echo 'Successfully Printed';


        

        // for ($i=1; $i < 2; $i++) { 
        //     $print_data = "^XA
        //     ^CFd0,10,18
        //     ^PR12
        //     ^LRY
        //     ^MD30
        //     ^PW350
        //     ^LL150
        //     ^PON
        //     ^FO44,20^BY1^B3N,N,73N,N^FDBARCODE^FS^FO189,2^GB0,146,6^FS^FO262,0^GB0,150,5^FS^PQ1
        //     ^XZ
        //     ";
        //     # code...
        //     fputs($fp,$print_data);
        // }
        // fclose($fp);

        // echo 'Successfully Printed';
    }
    catch (Exception $e) 
    {
        echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
});

Route::get('/{any}', function(){
    return view('welcome');
})->where('any', '.*')->name('home');
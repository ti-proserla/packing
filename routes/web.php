<?php

use Illuminate\Support\Facades\Route;
use App\Zebra\ZebraGenerate;
Route::get('zebra', function () {
    try
    {

        $string='{linea}{operador}{autonumerico}-{linea}{operador}{autonumerico}';
        $string="^XA
        ^BY2,1,80
        ^FO40,25^BCR,,,,,A^FD{linea}{operador}{autonumerico}^FS
        ^BY2,1,80
        ^FO200,25^BCR,,,,,A^FD{linea}{operador}{autonumerico}^FS
        ^BY2,1,80
        ^FO360,25^BCR,,,,,A^FD{linea}{operador}{autonumerico}^FS
        ^BY2,1,80
        ^FO520,25^BCR,,,,,A^FD{linea}{operador}{autonumerico}^FS
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
});

Route::get('/{any}', function(){
    return view('welcome');
})->where('any', '.*')->name('home');
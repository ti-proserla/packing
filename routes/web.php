<?php

use Illuminate\Support\Facades\Route;
use App\Zebra\ZebraGenerate;
Route::get('zebra', function () {
    try
    {
        $data=ZebraGenerate::text('Hola Mundo',10,20,0); 
        echo $data;
        $fp=pfsockopen("192.168.1.164",9100);
        fputs($fp,"^XA
        ^BYR2,1,90
        ^FO30,10^BCR^FD0100000001^FS
        
        ^BYR2,1,90
        ^FO150,10^BCR^FD0100000002^FS
        
        ^BYR2,1,80
        ^FO270,10^BCR^FD0100000003^FS
        
        ^BYR2,1,80
        ^FO390,10^BCR^FD0100000004^FS
        
        ^XZ");
        fclose($fp);
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
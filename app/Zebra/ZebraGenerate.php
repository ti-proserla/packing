<?php

namespace App\Zebra;

class ZebraGenerate
{
    public static function text($texto,$x,$y,$angulo){
        switch ($angulo) {
            case '0':
                $angulo="";
                break;
            case '90':
                $angulo="R";
                break;
            
            default:
                # code...
                break;
        }
        return "^XA
                ^FO$x,$y
                ^AD$angulo,30^FD$texto^FS
                ^XZ";
    }

}

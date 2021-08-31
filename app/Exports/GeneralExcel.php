<?php 
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class GeneralExcel implements FromArray, WithHeadings, ShouldAutoSize
{
    use Exportable;
    
    private $header;
    private $datos;

    public function __construct($datos)
    {
        $this->datos = $datos;
    }

    public function headings(): array
    {
        $header=[];
        if (count($header)>0) {
            foreach ($this->datos[0] as $key => $value) {
                array_push($header,ucwords(strtolower(str_replace("_"," ",$key))));
            }
        }
        return $header;
    }

    public function array(): array
    {
        return json_decode(json_encode($this->datos));
    }
}
<?php 
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class GeneralExcelHeading implements FromArray, WithHeadings, ShouldAutoSize
{
    use Exportable;
    
    private $datos;
    private $header;

    public function __construct($datos,$header)
    {
        $this->datos = $datos;
        $this->header = $header;
    }

    public function headings(): array
    {
        // dd($this->header);
        return $this->header;
    }

    public function array(): array
    {
        return json_decode(json_encode($this->datos));
    }
}
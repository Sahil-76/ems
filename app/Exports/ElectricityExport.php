<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ElectricityExport implements FromCollection,WithHeadings,WithMapping,ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    private $electricityUnits;

    function __construct($electricityUnits) {
        $this->electricityUnits = $electricityUnits;
    }

    public function collection()
    {
        return $this->electricityUnits;
    }

    public function map($electricity): array
    {
        return [
            getFormatedDate($electricity->date),
            $electricity->location ?? '',
            $electricity->start_unit ?? '',
            $electricity->end_unit ?? '',
            $electricity->total_units ?? '',
            $electricity->user->name ?? '',
        ];
    }

    public function headings() : array{
        return [
            'Date',
            'Location',
            'Start Unit',
            'End Unit',
            'Units Consumed',
            'Action By',
        ];
    }
}

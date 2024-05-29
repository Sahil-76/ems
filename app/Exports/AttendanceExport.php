<?php

namespace App\Exports;

use App\Models\Attendance;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AttendanceExport implements FromView,ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    private $data;
    
    public function __construct($data)
    {
        $this->data = $data;
    }
    
    

    public function view(): View
    {
        $data = $this->data;
        
        return view('attendance.export', $data);
    }
}

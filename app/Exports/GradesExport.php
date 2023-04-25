<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;


class GradesExport implements FromCollection, WithHeadings
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }
    public function collection()
    {
        return collect($this->data);
    }
    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Term_work',
            'Exam_work',
            'Total',
            'Grade',
        ];
    }

}

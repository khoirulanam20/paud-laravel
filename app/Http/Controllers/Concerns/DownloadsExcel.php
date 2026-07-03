<?php

namespace App\Http\Controllers\Concerns;

use App\Exports\ArraySheetExport;
use App\Exports\MultiSheetExport;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

trait DownloadsExcel
{
    /**
     * @param  list<string>  $headings
     * @param  list<list<mixed>>  $rows
     */
    protected function downloadExcel(array $headings, array $rows, string $filename, string $sheetTitle = 'Data'): BinaryFileResponse
    {
        return Excel::download(
            new ArraySheetExport($headings, $rows, $sheetTitle),
            $filename
        );
    }

    /**
     * @param  list<array{headings: list<string>, rows: list<list<mixed>>, title: string}>  $sheets
     */
    protected function downloadExcelSheets(array $sheets, string $filename): BinaryFileResponse
    {
        return Excel::download(new MultiSheetExport($sheets), $filename);
    }
}

<?php

namespace App\Traits;

use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Symfony\Component\HttpFoundation\StreamedResponse;

trait ExportableTrait
{
    /**
     * Stream an Excel (.xlsx) download using PhpSpreadsheet.
     *
     * @param string $filename   e.g. 'report.xlsx'
     * @param array  $headers    Column header labels
     * @param array  $rows       2-D array of data rows
     * @param string $sheetTitle Worksheet tab name
     */
    protected function exportToExcel(
        string $filename,
        array $headers,
        array $rows,
        string $sheetTitle = 'Export'
    ): StreamedResponse {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle(mb_substr($sheetTitle, 0, 31)); // Excel max 31 chars

        $colCount = count($headers);
        $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colCount);

        // ── Header row ────────────────────────────────────────────────
        foreach ($headers as $i => $label) {
            $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);
            $sheet->setCellValue("{$col}1", $label);
        }

        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
            'font' => [
                'bold'  => true,
                'color' => ['argb' => 'FFFFFFFF'],
                'size'  => 10,
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF1B6B3A'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'wrapText'   => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['argb' => 'FFB0B0B0'],
                ],
            ],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(22);

        // ── Data rows ─────────────────────────────────────────────────
        foreach ($rows as $rowIdx => $row) {
            $excelRow = $rowIdx + 2;
            foreach ($row as $colIdx => $value) {
                $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx + 1);
                $sheet->setCellValue("{$col}{$excelRow}", $value ?? '');
            }
            $bg = ($rowIdx % 2 === 0) ? 'FFFFFFFF' : 'FFF0F7F3';
            $sheet->getStyle("A{$excelRow}:{$lastCol}{$excelRow}")->applyFromArray([
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['argb' => $bg],
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color'       => ['argb' => 'FFE0E0E0'],
                    ],
                ],
            ]);
        }

        // ── Auto-fit columns ──────────────────────────────────────────
        for ($i = 1; $i <= $colCount; $i++) {
            $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $response = new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        });

        $response->headers->set(
            'Content-Type',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        );
        $response->headers->set('Content-Disposition', "attachment; filename=\"{$filename}\"");
        $response->headers->set('Cache-Control', 'max-age=0');

        return $response;
    }

    /**
     * Return a PDF download using DomPDF (landscape A4).
     *
     * @param string $filename e.g. 'report.pdf'
     * @param string $title    Page/document title
     * @param array  $headers  Column header labels
     * @param array  $rows     2-D array of data rows
     */
    protected function exportToPdf(
        string $filename,
        string $title,
        array $headers,
        array $rows
    ): \Illuminate\Http\Response {
        $pdf = Pdf::loadView('exports.table-pdf', compact('title', 'headers', 'rows'))
            ->setPaper('a4', 'landscape');

        return $pdf->download($filename);
    }
}

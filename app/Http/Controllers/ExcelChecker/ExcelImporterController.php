<?php

namespace App\Http\Controllers\ExcelChecker;

use App\Http\Controllers\Controller;
use App\Jobs\ExcelQueue;
use App\Models\Catalog;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use League\CommonMark\Extension\Table\Table;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExcelImporterController extends Controller
{
    public function index(Request $request)
    {
        $rows = DB::table('catalog')->limit(10)->paginate(10);
        // dd($rows);
        if (empty($rows)) {
            return view('view',  ['empty' => 'The Database is empty.']);
        } else {
            $databaseColumnNames = Schema::getColumnListing('catalog');

            return view('view', ['rows' => $rows, 'columns' => $databaseColumnNames]);
        }
    }
    public function Import(Request $request)
    {
        set_time_limit(500);
        ini_set('memory_limit', '10G');
        $request->validate([
            'excel_file' => 'required|mimes:csv,xls,xlsx'
        ]);

        $file = $request->file('excel_file');
        $filePath = $file->getPathname();

        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();

        $rowCount = $worksheet->getHighestRow();
        $batchChunks = [];

        if ($rowCount <= 1000) {
            // If the total row count is less than or equal to 1000, use the single chunk file
            $tempFilePath = 'excel_chunks/' . uniqid('excel_chunk_') . '.xlsx';

            // Save the original file as a temporary chunk file
            $file->move(storage_path('app/' . 'excel_chunks'), $tempFilePath);

            $batchChunks[] = storage_path('app/' . $tempFilePath);
        } else {
            // Split the data into chunks if the row count is greater than 1000
            for ($batchIndex = 1; $batchIndex <= ceil($rowCount / 10); $batchIndex++) {
                $currentBatchChunks = [];

                for ($chunkIndex = 1; $chunkIndex <= 10; $chunkIndex++) {
                    $currentChunkIndex = ($batchIndex - 1) * 10 + $chunkIndex;

                    if ($currentChunkIndex > 200) {
                        break;
                    }

                    $chunkSpreadsheet = new Spreadsheet();
                    $chunkWorksheet = $chunkSpreadsheet->getActiveSheet();

                    // Set the headers in the first row of each chunk
                    $headers = $worksheet->rangeToArray('A1:' . $worksheet->getHighestColumn() . '1', null, true, false);
                    $chunkWorksheet->fromArray($headers[0], null, 'A1');

                    // Set the rows for the current chunk
                    $startRow = ($currentChunkIndex - 1) * ceil($rowCount / 10) + 2;
                    $endRow = min($startRow + ceil($rowCount / 10) - 1, $rowCount);
                    $rows = $worksheet->rangeToArray('A' . $startRow . ':' . $worksheet->getHighestColumn() . $endRow, null, true, false);
                    $chunkWorksheet->fromArray($rows, null, 'A2');

                    // Save the chunk as a temporary file
                    $tempFilePath = 'excel_chunks/' . uniqid('excel_chunk_') . '.xlsx';
                    $writer = IOFactory::createWriter($chunkSpreadsheet, 'Xlsx');
                    $writer->save(storage_path('app/' . $tempFilePath));

                    $currentBatchChunks[] = storage_path('app/' . $tempFilePath);
                }

                $batchChunks[] = $currentBatchChunks;

                ExcelQueue::dispatch($currentBatchChunks);
            }
        }

        return redirect()->back()->with(['success' => 'File is importing']);
    }

    private function importBatchChunks($batchChunks)
    {
        // Importer Chunks
        foreach ($batchChunks as $chunkPath) {
            $chunkSpreadsheet = IOFactory::load($chunkPath);
            $chunkWorksheet = $chunkSpreadsheet->getActiveSheet();
            $requiredColumns = ['brand', 'category'];

            $rows = $chunkWorksheet->toArray();
            $headerRow = array_shift($rows); // Remove the header row from the rows array

            foreach ($rows as $row) {
                $data = array_combine($headerRow, $row);
                if ($this->validateRequiredColumns($data, $requiredColumns, $chunkPath)) {
                    $existingRow = Catalog::where('brand', $data['brand'])
                        ->where('mspn', $data['mspn'])
                        ->first();

                    if ($existingRow) {
                        // Row already exists in the database
                        $shouldUpdate = false;
                        foreach ($requiredColumns as $column) {
                            if ($data[$column] != $existingRow->$column) {
                                // Updated data found in one of the required columns, update the row
                                $shouldUpdate = true;
                                break;
                            }
                        }
                        if (!$shouldUpdate) {
                            // No updates found in required columns, skip this row
                            continue;
                        }
                    }

                    $primaryKey = ['brand' => $data['brand'], 'mspn' => $data['mspn']];
                    Catalog::updateOrCreate($primaryKey, $data);
                }
            }
            File::delete($chunkPath);
        }
    }


    private function validateRequiredColumns($data, $requiredColumns, $chunkPath)
    {
        foreach ($requiredColumns as $column) {
            if (empty($data[$column])) {
                return false;
            }
        }
        Storage::delete($chunkPath);
        return redirect()->back()->with(['error' => 'Excel is not match from database columns']);
    }

    public function export(Request $request)
    {
        $hiddenColumns = $request->input('hidden_columns', []);

        $table = new Catalog();
        $visibleColumns = array_diff($table->getFillable(), $hiddenColumns);

        $tableData = Catalog::select($visibleColumns)->get();

        // Create a new Spreadsheet object
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set the column headings
        $columnHeadings = array_keys($tableData->first()->toArray());
        $sheet->fromArray($columnHeadings, null, 'A1');

        // Set the table data
        $tableRows = $tableData->map(function ($row) use ($visibleColumns) {
            return collect($row->toArray())->only($visibleColumns)->values()->all();
        })->toArray();
        $sheet->fromArray($tableRows, null, 'A2');

        $writer = new Xlsx($spreadsheet);
        $filename = 'table_export.xlsx';

        // Save the spreadsheet to a file
        $writer->save($filename);

        // Download the spreadsheet
        return response()->download($filename)->deleteFileAfterSend(true);
    }
}

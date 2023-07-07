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
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use PhpOffice\PhpSpreadsheet\Style\Font;

class ExcelImporterController extends Controller
{
    public function index(Request $request)
    {
        $perPage = 10; // Number of rows per page
        $page = $request->query('page', 1);

        // Fetch the data from the database using pagination
        $rows = DB::table('catalog')->paginate($perPage, ['*'], 'page', $page);
        // dd($rows);
        if (empty($rows)) {
            return view('view',  ['empty' => 'The Database is empty.']);
        } else {
            $schemaManager = Schema::getConnection()->getDoctrineSchemaManager();
            $tableDetails = $schemaManager->listTableDetails('catalog');
            $databaseColumnNames = $tableDetails->getColumns();

            // Extract the column names from the column objects
            $columnNames = array_map(function ($column) {
                return $column->getName();
            }, $databaseColumnNames);

            $totalRows = DB::table('catalog')->count();

            // Calculate the range of rows being shown
            $startRow = ($page - 1) * $perPage + 1;
            $endRow = min($page * $perPage, $totalRows);

            return view('view', [
                'rows' => $rows,
                'columns' => $columnNames,
                'pagination' => $rows->links()->toHtml(),
                'totalRows' => $totalRows,
                'startRow' => $startRow,
                'endRow' => $endRow,
            ]);
        }
    }

    public function getData($page)
    {
        $perPage = 10; // Number of rows per page

        // Fetch the data from the database using pagination
        $rows = Catalog::paginate($perPage, ['*'], 'page', $page);

        // Render the view and pass the data
        $html = view('partials.rows')->with('rows', $rows)->render();
        $pagination = $rows->links()->toHtml();

        // Return the JSON response with the data and pagination links
        return response()->json([
            'data' => $html,
            'pagination' => $pagination,
        ]);
    }
    public function Import(Request $request)
    {
        set_time_limit(500);
        ini_set('memory_limit', '50G');
        $request->validate([
            'excel_file' => 'required|mimes:csv,xls,xlsx'
        ]);

        $file = $request->file('excel_file');
        $filePath = $file->getPathname();

        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();

        $rows = $worksheet->toArray();
        $highestRow = $rows[0];
        $sliceHighestRow = array_slice($rows, 1);
        $collection = collect($highestRow);

        $dataIndexNames = $collection->values()->toArray();
        $dataIndexNamesString = implode(', ', $dataIndexNames);
        // dd($dataIndexNamesString);

        $databaseColumnNames = Schema::getColumnListing('catalogs');
        array_shift($databaseColumnNames);
        $indexNamesString = implode(', ', $databaseColumnNames);
        // dd($indexNamesString);

        $areColumnsEqual = ($dataIndexNamesString === $indexNamesString);
        // dd($areColumnsEqual);

        if (!$areColumnsEqual) {
            return redirect()->back()->with(['error' => 'There is error in the column header']);
        }
        $temporaryPath = 'excel_chunks/' . $file->getClientOriginalName();
        Storage::disk('local')->put($temporaryPath, file_get_contents($file));

        ExcelQueue::dispatch($temporaryPath)->onQueue('imports');

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

    public function export(Request $request)
    {
        $hiddenColumns = $request->input('hidden_columns', []);

        $table = new Catalog();
        $visibleColumns = array_diff($table->getFillable(), $hiddenColumns);

        // Get the column headings from the database (excluding "id" column)
        $schemaManager = Schema::getConnection()->getDoctrineSchemaManager();
        $tableDetails = $schemaManager->listTableDetails('catalog');
        $databaseColumnNames = $tableDetails->getColumns();

        // Extract the column names from the column objects
        $columnNames = array_map(function ($column) {
            return $column->getName();
        }, $databaseColumnNames);
        $columnHeadings = array_filter($columnNames, function ($column) {
            return $column !== 'id';
        });
        // Create a new Spreadsheet object
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set the column headings
        $sheet->fromArray([$columnHeadings], null, 'A1');

        // Set dropdown filters and bold font for column headings
        $lastColumnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($columnHeadings));
        $filterRange = 'A1:' . $lastColumnLetter . '1';
        $sheet->setAutoFilter($filterRange);

        $boldFont = new Font();
        $boldFont->setBold(true);
        $sheet->getStyle($filterRange)->getFont()->setBold(true);

        // Set the width of the columns
        foreach (range('A', $lastColumnLetter) as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $filename = 'Catalog Template.xlsx';

        // Save the spreadsheet to a file
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($filename);

        // Download the spreadsheet
        return response()->download($filename)->deleteFileAfterSend(true);
    }
}
